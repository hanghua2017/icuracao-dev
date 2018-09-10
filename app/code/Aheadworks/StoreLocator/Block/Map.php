<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block;

use Aheadworks\StoreLocator\Helper\Config;
use Aheadworks\StoreLocator\Helper\Image;
use Aheadworks\StoreLocator\Model\Location\Json;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\Encoder;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Map.
 */
class Map extends Template
{
    /**
     * @var AbstractCollection
     */
    public $collection = null;

    /**
     * @var Config
     */
    protected $helperConfig;

    /**
     * @var Image
     */
    protected $helperImage;

    /**
     * @var Json
     */
    protected $locationJson;

    /**
     * @var Encoder
     */
    protected $jsonEncoder;

    /**
     * Construct
     *
     * @param Context $context
     * @param Config $helperConfig
     * @param Image $helperImage
     * @param Json $locationJson
     * @param Encoder $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $helperConfig,
        Image $helperImage,
        Json $locationJson,
        Encoder $jsonEncoder,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperConfig = $helperConfig;
        $this->helperImage = $helperImage;
        $this->locationJson = $locationJson;
        $this->jsonEncoder = $jsonEncoder;
    }

    /**
     * Get Google Maps API key.
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->helperConfig->getGoogleMapsApiKey() ? '&key=' . $this->helperConfig->getGoogleMapsApiKey(): '';
    }

    /**
     * @param AbstractCollection $collection
     * @return void
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return AbstractCollection
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Retrieve location data.
     *
     * @return string
     */
    public function getLocationDataJson()
    {
        return $this->locationJson->getDataJson($this->getCollection());
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        $this->addBreadcrumbs();

        $this->pageConfig->setKeywords($this->helperConfig->getMetaKeywords());
        $this->pageConfig->setDescription($this->helperConfig->getMetaDescription());

        $pageMainTitle = $this->getLayout()->getBlock('page.main.title');
        if ($pageMainTitle) {
            $pageMainTitle->setPageTitle($this->escapeHtml($this->helperConfig->getTitle()));
        }
        return parent::_prepareLayout();
    }

    /**
     * Prepare breadcrumbs.
     *
     * @throws LocalizedException
     * @return void
     */
    protected function addBreadcrumbs()
    {
        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb(
                'home',
                [
                    'label' => __('Home'),
                    'title' => __('Go to Home Page'),
                    'link' => $this->_storeManager->getStore()->getBaseUrl()
                ]
            );
            $breadcrumbsBlock->addCrumb(
                'aw_store_locator',
                ['label' => $this->helperConfig->getTitle(), 'title' => $this->helperConfig->getTitle()]
            );
        }
    }
}
