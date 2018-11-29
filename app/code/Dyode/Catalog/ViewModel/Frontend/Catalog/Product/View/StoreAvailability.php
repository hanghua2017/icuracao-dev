<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package Dyode
 * @module  Dyode_Catalog
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View;

use Aheadworks\StoreLocator\Helper\Image as AheadImageHelper;
use Aheadworks\StoreLocator\Model\Location;
use Aheadworks\StoreLocator\Model\ResourceModel\Location\CollectionFactory;
use Dyode\ArInvoice\Helper\Data as ArInvoiceHelper;
use Dyode\InventoryLocation\Model\ResourceModel\Location\CollectionFactory as InventoryLocationCollectionFactory;
use Dyode\StoreLocator\Model\GeoCoordinateRepository;
use Magento\Catalog\Model\Product;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json as JsonHelper;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class StoreAvailability implements ArgumentInterface
{
    const SHIPPING_DAYS_LABEL_CONFIG_PATH = 'curacao_catalog/curacao_product_page/shipping_days_label';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Dyode\ArInvoice\Helper\Data
     */
    protected $arInvoiceHelper;

    /**
     * @var \Dyode\StoreLocator\Model\GeoCoordinateRepository
     */
    protected $geoCoordinateRepository;

    /**
     * @var \Aheadworks\StoreLocator\Model\LocationFactory
     */
    protected $locationFactory;

    /**
     * @var \Aheadworks\StoreLocator\Model\ResourceModel\Location\CollectionFactory
     */
    protected $locationCollectionFactory;

    /**
     * @var \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    protected $customerGeoCoordinate;

    /**
     * @var \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection
     */
    protected $productStores;

    /**
     * @var array
     */
    protected $productStoresGeoCoordinates;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaInterface
     */
    protected $searchCriteria;

    /**
     * @var \Aheadworks\StoreLocator\Helper\Image
     */
    protected $aheadImageHelper;

    /**
     * @var \Dyode\InventoryLocation\Model\ResourceModel\Location\CollectionFactory
     */
    protected $inventoryLocationCollectionFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * @var []
     */
    protected $productAvailStores = [];

    /**
     * StoreAvailability constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Aheadworks\StoreLocator\Model\ResourceModel\Location\CollectionFactory $locationCollectionFactory
     * @param \Dyode\InventoryLocation\Model\ResourceModel\Location\CollectionFactory $inventoryLocationCollectionFactory
     * @param \Dyode\StoreLocator\Model\GeoCoordinateRepository $geoCoordinateRepository
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     * @param \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
     * @param \Aheadworks\StoreLocator\Helper\Image $aheadImageHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SearchCriteriaInterface $searchCriteria,
        Session $customerSession,
        CollectionFactory $locationCollectionFactory,
        InventoryLocationCollectionFactory $inventoryLocationCollectionFactory,
        GeoCoordinateRepository $geoCoordinateRepository,
        Registry $registry,
        JsonHelper $jsonHelper,
        ArInvoiceHelper $arInvoiceHelper,
        AheadImageHelper $aheadImageHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->searchCriteria = $searchCriteria;
        $this->customerSession = $customerSession;
        $this->locationCollectionFactory = $locationCollectionFactory;
        $this->inventoryLocationCollectionFactory = $inventoryLocationCollectionFactory;
        $this->arInvoiceHelper = $arInvoiceHelper;
        $this->geoCoordinateRepository = $geoCoordinateRepository;
        $this->coreRegistry = $registry;
        $this->aheadImageHelper = $aheadImageHelper;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Collect shipping days label from store config.
     *
     * @return string
     */
    public function shippingDaysLabel()
    {
        return $this->scopeConfig->getValue(self::SHIPPING_DAYS_LABEL_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }

    /**
     * If customer is not logged in or customer do not have shipping address or there is no stores available,
     * then we don't want to show the nearest store.
     *locationFactory
     *
     * @return bool
     */
    public function canShowNearestStore()
    {
        if (!$this->customerSession->isLoggedIn()) {
            return false;
        }

        if (!$this->getCustomer()->getDefaultShippingAddress()
            || !$this->getCustomer()->getDefaultShippingAddress()->getPostcode()
        ) {
            return false;
        }

        if (!$this->customerGeoCoordinate() || !$this->customerGeoCoordinate()->getId()) {
            return false;
        }

        if (!$this->availableStores()
            || !$this->availableStores()->getItems()
            || count(!$this->availableStores()->getItems()) === 0
        ) {
            return false;
        }

        return true;
    }

    /**
     * Collect customer location information by zipcode available in the shipping address.
     *
     * @return bool|\Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     */
    public function customerGeoCoordinate()
    {
        if (!$this->customerGeoCoordinate) {
            $zipCode = $this->getCustomer()->getDefaultShippingAddress()->getPostcode();
            // Handle cases where zipcode is not available
            try {
                $this->customerGeoCoordinate = $this->geoCoordinateRepository->getById($zipCode);
            } catch (\Magento\Framework\Exception\LocalizedException $exception) {
                return false;
            }
        }

        return $this->customerGeoCoordinate;

    }

    /**
     * Location information of stores available against the product.
     *
     * @return array|\Dyode\StoreLocator\Api\Data\GeoCoordinateSearchResultsInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function storeGeoCoordinates()
    {
        if (!$this->productStoresGeoCoordinates) {
            $storeZipCodes = $this->availableStores()->getColumnValues('zip');
            $filter = new Filter([
                'field'          => 'zip',
                'value'          => $storeZipCodes,
                'condition_type' => 'in',
            ]);
            $filterGroup = new FilterGroup();
            $filterGroup->setFilters([$filter]);
            $searchCriteria = $this->searchCriteria->setFilterGroups([$filterGroup]);
            $this->productStoresGeoCoordinates = $this->geoCoordinateRepository->getList($searchCriteria);
        }

        return $this->productStoresGeoCoordinates;
    }

    /**
     * Find Location of a store from the store location collection.
     *
     * @param \Aheadworks\StoreLocator\Model\Location $location
     * @return bool|\Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function findStoreLocationGeoCoordiante(Location $location)
    {
        foreach ($this->storeGeoCoordinates()->getItems() as $storeGeoCoordinate) {
            if ($location->getZip() == $storeGeoCoordinate->getZip()) {
                return $storeGeoCoordinate;
            }
        }

        return false;
    }

    /**
     * Provide nearest store location to the customer.
     *
     * @return bool|\Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function nearestStore()
    {
        $stores = [];
        foreach ($this->availableStores() as $storeLocation) {
            /** @var  $storeLocation \Aheadworks\StoreLocator\Model\Location */
            $storeLocationGeoCoordinate = $this->findStoreLocationGeoCoordiante($storeLocation);

            if ($storeLocationGeoCoordinate) {
                $storeDistance = (float)$this->arInvoiceHelper->getDistance(
                    $this->customerGeoCoordinate()->getLat(),
                    $this->customerGeoCoordinate()->getLng(),
                    $storeLocationGeoCoordinate->getLat(),
                    $storeLocationGeoCoordinate->getLng()
                );

                $stores[$storeDistance] = new DataObject([
                    'code' => $storeLocation->getStoreLocationCode(),
                    'name' => $storeLocation->getTitle(),
                ]);
            }
        }

        if (count($stores) === 0) {
            return false;
        }

        ksort($stores);

        //return first item
        foreach ($stores as $store) {
            return $store;
        }
    }

    /**
     * Collect stores in which product is available.
     *
     * @return \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection
     */
    public function availableStores()
    {
        if (!$this->productStores) {
            /** @var $collection \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection */
            $collection = $this->locationCollectionFactory->create();

            //avoid "All Stores" which we don't want to list as a store.
            $this->productStores = $collection->addFieldToFilter('region_id', ['nin' => [1]]);
        }

        return $this->productStores;
    }


    /**
     * Customer who is logged in.
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->customerSession->getCustomer();
    }

    /**
     * Prepare address information as an array from the store location.
     *
     * @param \Aheadworks\StoreLocator\Model\Location $storeLocation
     * @return array
     */
    public function storeAddress(Location $storeLocation)
    {
        return array_filter([
            'title'  => $storeLocation->getTitle(),
            'street' => $storeLocation->getStreet(),
            'city'   => $storeLocation->getCity(),
            'phone'  => $storeLocation->getPhone(),
        ]);
    }

    /**
     * Image helper
     *
     * @return \Aheadworks\StoreLocator\Helper\Image
     */
    public function storeLocationImgHelper()
    {
        return $this->aheadImageHelper;
    }

    /**
     * Customer shipping address zip code.
     *
     * @return string
     */
    /**
     * Customer shipping address zip code.
     *
     * @return string
     */
    public function customerZipCode()
    {
        $postCode = '';

        if ($this->customerSession->isLoggedIn()) {
            $shippingAddress = $this->getCustomer()->getDefaultShippingAddress();
            if ($shippingAddress && $shippingAddress->getPostcode()) {
                $postCode = $shippingAddress->getPostcode();
            }
        }

        return $postCode;
    }

    /**
     * AR Invoice Helper
     *
     * @return \Dyode\ArInvoice\Helper\Data
     */
    public function arInvoiceHelper()
    {
        return $this->arInvoiceHelper;
    }

    /**
     * Collect product store codes corresponding to a product from store_inventory level table which is updating
     * via a cron job.
     *
     * @param string|integer|\Magento\Catalog\Model\Product $product
     * @return array
     */
    public function productAvailableStores($product)
    {
        $productId = $product;
        if ($product instanceof Product) {
            $productId = $product->getId();
        }

        if (isset($this->productAvailStores[$productId])) {
            return $this->productAvailStores[$productId];
        }

        /** @var \Dyode\InventoryLocation\Model\ResourceModel\Location\Collection $inventoryCollection */
        $storesList = [];
        $productAvailStores = [];

        $inventoryCollection = $this->inventoryLocationCollectionFactory->create();
        $inventoryCollection->addFieldToFilter('productid', $productId)->setPageSize(1)->setCurPage(1);

        foreach ($inventoryCollection->getItems() as $inventory) {
            $storesList = $this->jsonHelper->unserialize($inventory->getFinalinventory());
        }

        foreach ($storesList as $store => $inventoryLevel) {
            if ($inventoryLevel > 0) {
                $productAvailStores[] = (int)$store;
            }
        }

        $this->productAvailStores[$productId] = $productAvailStores;

        return $this->productAvailStores[$productId];
    }

    /**
     * Provide JSData to the dyode.storeAvailability jQuery Widget.
     *
     * @return bool|false|string
     */
    public function storeAvailabilityJsData()
    {
        $jsData = [
            'customer' => [
                'zip' => $this->customerZipCode(),
            ],
            'product'  => [
                'id' => $this->getCurrentProduct()->getId(),
            ],
        ];

        return $this->jsonHelper->serialize($jsData);
    }

    /**
     * Retrieve current product model
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getCurrentProduct()
    {
        return $this->coreRegistry->registry('product');
    }
}
