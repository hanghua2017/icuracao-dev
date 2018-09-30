<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;

/**
 * Class Config.
 */
class Config extends AbstractHelper
{
    /**
     * Parameters XML paths
     */
    const XML_PATH_ENABLE_STORE_LOCATOR = 'aw_store_locator/general/enable_store_locator';

    const XML_PATH_GOOGLE_MAPS_API_KEY = 'aw_store_locator/general/google_maps_api_key';

    const XML_PATH_TITLE = 'aw_store_locator/general/title';

    const XML_PATH_URL_KEY = 'aw_store_locator/general/url_key';

    const XML_PATH_URL_TO_TOP_MENU = 'aw_store_locator/general/url_to_top_menu';

    const XML_PATH_CMS_BLOCK = 'aw_store_locator/general/header_block';

    const XML_PATH_ENABLE_FIND_MY_LOCATION_BUTTON = 'aw_store_locator/general/enable_find_my_location_button';

    const XML_PATH_SEARCH_RADIUS = 'aw_store_locator/search/search_radius_values';

    const XML_PATH_DEFAULT_SEARCH_RADIUS = 'aw_store_locator/search/default_search_radius';

    const XML_PATH_DEFAULT_SEARCH_MEASUREMENT = 'aw_store_locator/search/default_search_measurement';

    const XML_PATH_META_KEYWORDS = 'aw_store_locator/seo/meta_keywords';

    const XML_PATH_META_DESCRIPTION = 'aw_store_locator/seo/meta_description';

    /**
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Retrieve Google Maps API key.
     *
     * @param Store|string|int $store
     * @return string
     */
    public function getGoogleMapsApiKey($store = null)
    {
        $apiKey = (string)$this->scopeConfig->getValue(
            self::XML_PATH_GOOGLE_MAPS_API_KEY,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $apiKey ? '&key=' . $apiKey : '';
    }

    /**
     * Retrieve top menu item status.
     *
     * @param Store|string|int $store
     * @return int
     */
    public function getTopMenuItem($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_URL_TO_TOP_MENU,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve title.
     *
     * @param Store|string|int $store
     * @return string
     */
    public function getTitle($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_TITLE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve URL key.
     *
     * @param Store|string|int $store
     * @return string
     */
    public function getUrlKey($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_URL_KEY,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve CMS block.
     *
     * @param Store|string|int $store
     * @return string
     */
    public function getCmsBlock($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_CMS_BLOCK,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve `Find My Location` button status.
     *
     * @param Store|string|int $store
     * @return int
     */
    public function isFindMyLocationButtonEnabled($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_FIND_MY_LOCATION_BUTTON,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve page status.
     *
     * @param Store|string|int $store
     * @return int
     */
    public function isStoreLocatorPageEnabled($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_ENABLE_STORE_LOCATOR,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve search radius values.
     *
     * @param Store|string|int $store
     * @return string
     */
    public function getSearchRadius($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_SEARCH_RADIUS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve default search radius value.
     *
     * @param Store|string|int $store
     * @return string
     */
    public function getDefaultSearchRadius($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_SEARCH_RADIUS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve default search measurement.
     *
     * @param Store|string|int $store
     * @return string
     */
    public function getDefaultSearchMeasurement($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_DEFAULT_SEARCH_MEASUREMENT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve meta keywords.
     *
     * @param Store|string|int $store
     * @return string
     */
    public function getMetaKeywords($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_META_KEYWORDS,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Retrieve meta description.
     *
     * @param Store|string|int $store
     * @return string
     */
    public function getMetaDescription($store = null)
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_META_DESCRIPTION,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
