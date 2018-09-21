<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block\Adminhtml\Location\Edit\Tab;

use Aheadworks\StoreLocator\Helper\Config;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;

/**
 * Class GoogleMap.
 */
class GoogleMap extends Generic implements TabInterface
{
    /**
     * @var Config
     */
    protected $helperConfig;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Config $helperConfig
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Config $helperConfig,
        array $data = []
    ) {
        $this->helperConfig = $helperConfig;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /** @var \Aheadworks\StoreLocator\Model\Location $location */
        $location = $this->_coreRegistry->registry('aheadworks_location');

        if ($this->isAllowed('Aheadworks_StoreLocator::location_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('location_');

        $fieldset = $form->addFieldset('google_map_fieldset', ['legend' => __('Google Maps Settings')]);

        $this->_addElementTypes($fieldset);

        $fieldset->addField(
            'zoom',
            'text',
            [
                'name' => 'zoom',
                'label' => __('Zoom'),
                'title' => __('Zoom'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'latitude',
            'text',
            [
                'name' => 'latitude',
                'label' => __('Latitude'),
                'title' => __('Latitude'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'longitude',
            'text',
            [
                'name' => 'longitude',
                'label' => __('Longitude'),
                'title' => __('Longitude'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $findStoreButton = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class,
            '',
            [
                'data' => [
                    'type' => 'button',
                    'id' => 'find-store-button',
                    'label' => __('Find Store on Map'),
                    'class' => 'add',
                ]
            ]
        );

        $fieldset->addField(
            'find_store_button',
            'note',
            [
                'name' => 'find_store_button',
                'text' => $findStoreButton->toHtml(),
                'note' => __(
                    'Find store location according to entered country, city and street address (General Information)'
                )
            ]
        );

        $fieldset->addField(
            'google_map',
            'google_map',
            [
                'name' => 'google_map',
                'api_key' => $this->helperConfig->getGoogleMapsApiKey(),
                'zoom' => $location->getZoom(),
                'latitude' => $location->getLatitude(),
                'longitude' => $location->getLongitude()
            ]
        );

        $form->setFieldNameSuffix('location');

        $form->addValues($location->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * {@inheritdoc}
     */
    protected function _getAdditionalElementTypes()
    {
        return [
            'google_map' => \Aheadworks\StoreLocator\Block\Adminhtml\Form\Element\GoogleMap::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Google Maps Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Google Maps Settings');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * @param string $resourceId
     * @return bool
     */
    protected function isAllowed($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }
}
