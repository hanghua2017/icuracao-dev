<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block\Adminhtml\Form\Element;

use Aheadworks\StoreLocator\Model\Config;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;

/**
 * Class GoogleMap.
 */
class GoogleMap extends AbstractElement
{
    /**
     * Element output template.
     */
    const ELEMENT_OUTPUT_TEMPLATE = 'Aheadworks_StoreLocator::location/form/element/map.phtml';

    /**
     * @var LayoutInterface
     */
    protected $layout;

    /**
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param LayoutInterface $layout
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        LayoutInterface $layout,
        array $data = []
    ) {
        $this->layout = $layout;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

        $this->setType('google_map');
    }

    /**
     * {@inheritdoc}
     */
    public function getElementHtml()
    {
        $block = $this->createElementHtmlOutputBlock();
        $this->assignBlockVariables($block);

        return $block->toHtml();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultHtml()
    {
        $html = $this->getData('default_html');
        $html .= $this->getElementHtml();

        return $html;
    }

    /**
     * {@inheritdoc}
     */
    public function getHtml()
    {
        $html = $this->getDefaultHtml();

        return $html;
    }

    /**
     * @param Template $block
     * @return Template
     */
    public function assignBlockVariables(Template $block)
    {
        $block->assign([
            'apiKey' => $this->getApiKey(),
            'zoom' => $this->getZoom(),
            'latitude' => $this->getLatitude(),
            'longitude' => $this->getLongitude()
        ]);

        return $block;
    }

    /**
     * Get api key.
     *
     * @return float
     */
    private function getApiKey()
    {
        return $this->getData('api_key');
    }

    /**
     * Get zoom.
     *
     * @return int
     */
    private function getZoom()
    {
        return $this->getData('zoom') ?: Config::DEFAULT_ZOOM;
    }

    /**
     * Get latitude.
     *
     * @return float
     */
    private function getLatitude()
    {
        return $this->getData('latitude') ?: Config::DEFAULT_LATITUDE;
    }

    /**
     * Get longitude.
     *
     * @return float
     */
    private function getLongitude()
    {
        return $this->getData('longitude') ?: Config::DEFAULT_LONGITUDE;
    }

    /**
     * @return Template
     */
    public function createElementHtmlOutputBlock()
    {
        /** @var Template $block */
        $block = $this->layout->createBlock(
            Template::class,
            'aheadworks_store_locator.location.form.google_map.element'
        );

        $block->setTemplate(self::ELEMENT_OUTPUT_TEMPLATE);

        return $block;
    }
}
