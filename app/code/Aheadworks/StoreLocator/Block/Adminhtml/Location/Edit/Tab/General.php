<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block\Adminhtml\Location\Edit\Tab;

use Aheadworks\StoreLocator\Block\Adminhtml\Form\Element\Image;
use Aheadworks\StoreLocator\Model\Config;
use Aheadworks\StoreLocator\Model\Config\Source\Status;
use Aheadworks\StoreLocator\Model\Config\Source\StatusFactory;
use Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form;
use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Directory\Model\Config\Source\Country;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Magento\Store\Model\System\Store;

/**
 * Class General.
 */
class General extends Generic implements TabInterface
{
    /**
     * @var Store
     */
    protected $systemStore;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var Country
     */
    protected $country;

    /**
     * @var Status
     */
    protected $statusFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param Store $systemStore
     * @param RegionFactory $regionFactory
     * @param Country $country
     * @param StatusFactory $statusFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        Store $systemStore,
        RegionFactory $regionFactory,
        Country $country,
        StatusFactory $statusFactory,
        array $data = []
    ) {
        $this->systemStore = $systemStore;
        $this->regionFactory = $regionFactory;
        $this->country = $country;
        $this->statusFactory = $statusFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareForm()
    {
        /* @var \Aheadworks\StoreLocator\Model\Location $location */
        $location = $this->_coreRegistry->registry('aheadworks_location');

        if ($this->isAllowed('Aheadworks_StoreLocator::location_save')) {
            $isElementDisabled = false;
        } else {
            $isElementDisabled = true;
        }

        if (!$location->getCountryId()) {
            $location->setCountryId(
                Config::DEFAULT_LOCATION_COUNTRY
            );
        }

        if (!$location->getRegionId()) {
            $location->setRegionId(
                Config::DEFAULT_LOCATION_REGION
            );
        }

        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('location_');

        $fieldset = $form->addFieldset('general_details_fieldset', ['legend' => __('General Information')]);

        if ($location->getLocationId()) {
            $fieldset->addField('location_id', 'hidden', ['name' => 'location_id']);
        }

        $status = $this->statusFactory->create()->toOptionArray();

        $fieldset->addField(
            'status',
            'select',
            [
                'name' => 'status',
                'label' => __('Status'),
                'title' => __('Status'),
                'required' => true,
                'values'   => $status,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'title',
            'text',
            [
                'name' => 'title',
                'label' => __('Title'),
                'title' => __('Title'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'store_location_code',
            'text',
            [
                'name' => 'store_location_code',
                'label' => __('Store Location Code'),
                'title' => __('Store Location Code'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'store_manager',
            'text',
            [
                'name' => 'store_manager',
                'label' => __('Store Manager'),
                'title' => __('Store Manager'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'store_email',
            'text',
            [
                'name' => 'store_email',
                'label' => __('Store Email'),
                'title' => __('Store Email'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $countries = $this->getCountries();
        $fieldset->addField(
            'country_id',
            'select',
            [
                'name' => 'country_id',
                'label' => __('Country'),
                'title' => __('Country'),
                'required' => true,
                'values' => $countries,
                'disabled' => $isElementDisabled
            ]
        );

        $regions = $this->getRegions($location->getCountryId());
        $fieldset->addField(
            'region_id',
            'select',
            [
                'name' => 'region_id',
                'label' => __('State'),
                'title' => __('State'),
                'required' => false,
                'values' => $regions,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'city',
            'text',
            [
                'name' => 'city',
                'label' => __('City'),
                'title' => __('City'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'street',
            'text',
            [
                'name' => 'street',
                'label' => __('Street'),
                'title' => __('Street'),
                'required' => true,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'zip',
            'text',
            [
                'name' => 'zip',
                'label' => __('Zip'),
                'title' => __('Zip'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'phone',
            'text',
            [
                'name' => 'phone',
                'label' => __('Phone'),
                'title' => __('Phone'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'fax',
            'text',
            [
                'name' => 'fax',
                'label' => __('Fax'),
                'title' => __('Fax'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField(
                'stores',
                'multiselect',
                [
                    'name' => 'stores[]',
                    'label' => __('Store View'),
                    'title' => __('Store View'),
                    'required' => true,
                    'values' => $this->systemStore->getStoreValuesForForm(false, true)
                ]
            );
            $renderer = $this->getLayout()->createBlock(
                Element::class
            );
            $field->setRenderer($renderer);
        } else {
            $fieldset->addField(
                'stores',
                'hidden',
                ['name' => 'stores[]', 'value' => $this->_storeManager->getStore(true)->getId()]
            );
            $location->setStores($this->_storeManager->getStore(true)->getId());
        }

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name' => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name' => 'sort_order',
                'label' => __('Sort Order'),
                'title' => __('Sort Order'),
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'image',
            Image::class,
            [
                'name' => 'image',
                'label' => __('Location Image'),
                'title' => __('Location Image'),
                'location_id' => $location->getLocationId(),
                'width' => '88',
                'height' => '88',
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $fieldset->addField(
            'custom_marker',
            Image::class,
            [
                'name' => 'custom_marker',
                'label' => __('Google Map Marker'),
                'title' => __('Google Map Marker'),
                'location_id' => $location->getLocationId(),
                'width' => '20',
                'height' => '32',
                'required' => false,
                'disabled' => $isElementDisabled
            ]
        );

        $form->setFieldNameSuffix('location');

        $form->setValues($location->getData());
        $this->setForm($form);

        $this->setChild(
            'form_after',
            $this->getLayout()
                ->createBlock(Template::class)
                ->setTemplate('Aheadworks_StoreLocator::location/js-general.phtml')
        );

        return parent::_prepareForm();
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('General Information');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('General Information');
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

    /**
     * @return array
     */
    protected function getCountries()
    {
        $countries = $this->country->toOptionArray(false, '');
        unset($countries[0]);

        return $countries;
    }

    /**
     * @param string $countryId
     * @return array
     */
    protected function getRegions($countryId)
    {
        $regionCollection = $this->regionFactory->create()->getCollection()->addCountryFilter(
            $countryId
        );

        $regions = $regionCollection->toOptionArray();
        if ($regions) {
            $regions[0]['label'] = '';
        } else {
            $regions = [['value' => '', 'label' => '']];
        }

        return $regions;
    }
}
