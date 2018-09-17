<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block;

use Aheadworks\StoreLocator\Helper\Config;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Html\Select;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

/**
 * Class Search.
 */
class Search extends Template
{
    /**
     * @var Config
     */
    protected $helperConfig;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @param Context $context
     * @param Config $helperConfig
     * @param RegionFactory $regionFactory
     * @param CountryFactory $countryFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $helperConfig,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->helperConfig = $helperConfig;
        $this->regionFactory = $regionFactory;
        $this->countryFactory = $countryFactory;
    }

    /**
     * Retrieve search radius select element html.
     *
     * @return string
     * @throws LocalizedException
     */
    public function getSearchRadiusElementHtml()
    {
        $options = [
            ['value' => '', 'label' => __('Everywhere')]
        ];

        $searchRadius = explode(',', $value = $this->helperConfig->getSearchRadius());
        if (!is_array($searchRadius)) {
            return false;
        }

        $defaultSearchRadius = $this->helperConfig->getDefaultSearchRadius();
        if (!in_array($defaultSearchRadius, $searchRadius)) {
            array_push($searchRadius, $defaultSearchRadius);
            sort($searchRadius);
        }

        foreach ($searchRadius as $option) {
            $options[] = ['value'  => $option, 'label' => $option];
        }

        $radius = $this->getRadius();
        $value = ($radius || $radius === '') ? $this->getRadius() : $defaultSearchRadius;
        return $this->getSelectBlock()->setName(
            'search[radius]'
        )->setId(
            'aw-storelocator-search-block-radius'
        )->setTitle(
            __('Search Radius')
        )->setExtraParams(
            ''
        )->setValue(
            $value
        )->setOptions(
            $options
        )->getHtml();
    }

    /**
     * Retrieve search measurement select element html.
     *
     * @return string
     * @throws LocalizedException
     */
    public function getSearchMeasurementElementHtml()
    {
        $options = [
            ['value' => 'km', 'label' => __('km')],
            ['value' => 'mi', 'label' => __('mi')]
        ];

        $value = $this->getMeasurement() ? $this->getMeasurement() : $this->helperConfig->getDefaultSearchMeasurement();
        return $this->getSelectBlock()->setName(
            'search[measurement]'
        )->setId(
            'aw-storelocator-search-block-measurement'
        )->setTitle(
            __('Search Measurement')
        )->setExtraParams(
            ''
        )->setValue(
            $value
        )->setOptions(
            $options
        )->getHtml();
    }

    /**
     * Retrieve country select element html.
     *
     * @return string
     * @throws LocalizedException
     */
    public function getCountryElementHtml()
    {
        $countryCollection = $this->countryFactory->create()->getCollection();

        $options = $countryCollection->toOptionArray();
        $options[0]['label'] = '';

        $value = $this->getCountryId();
        return $this->getSelectBlock()->setName(
            'search[country_id]'
        )->setId(
            'aw-storelocator-search-block-country'
        )->setTitle(
            __('Country')
        )->setExtraParams(
            ''
        )->setValue(
            $value
        )->setOptions(
            $options
        )->getHtml();
    }

    /**
     * Retrieve region select element html.
     *
     * @return string
     * @throws LocalizedException
     */
    public function getRegionElementHtml()
    {
        $country = !$this->getCountryId() ? '': $this->getCountryId();
        $regionCollection = $this->regionFactory->create()->getCollection()->addCountryFilter(
            $country
        );

        $options = $regionCollection->toOptionArray();
        if ($options) {
            $options[0]['label'] = '';
        } else {
            $options = [['value' => '', 'label' => '']];
        }

        $value = $this->getRegionId();
        return $this->getSelectBlock()->setName(
            'search[region_id]'
        )->setId(
            'aw-storelocator-search-block-region'
        )->setTitle(
            __('Region')
        )->setExtraParams(
            ''
        )->setValue(
            $value
        )->setOptions(
            $options
        )->getHtml();
    }

    /**
     * @return BlockInterface
     * @throws LocalizedException
     */
    protected function getSelectBlock()
    {
        $block = $this->getData('_select_block');
        if ($block === null) {
            $block = $this->getLayout()->createBlock(Select::class);
            $this->setData('_select_block', $block);
        }
        return $block;
    }

    /**
     * Return formatted field value.
     *
     * @param string $value Search field value
     * @return string
     */
    public function getFormattedValue($value)
    {
        return $this->_escaper->escapeHtml($value);
    }

    /**
     * Return the status of `Find My Location` button.
     *
     * @return int
     */
    public function isFindMyLocationButtonEnabled()
    {
        $buttonEnabled = $this->helperConfig->isFindMyLocationButtonEnabled();
        $isCurrentlySecure = $this->_storeManager->getStore()->isCurrentlySecure();

        if ($buttonEnabled && !$isCurrentlySecure) {
            $buttonEnabled = false;
        }

        return $buttonEnabled;
    }
}
