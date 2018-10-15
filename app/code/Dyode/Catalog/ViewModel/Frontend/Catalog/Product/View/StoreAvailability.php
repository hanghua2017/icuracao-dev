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
use Aheadworks\StoreLocator\Model\LocationFactory;
use Dyode\ArInvoice\Helper\Data as ArInvoiceHelper;
use Dyode\StoreLocator\Model\GeoCoordinateRepository;
use Magento\Customer\Model\Session;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObject;
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
     * StoreAvailability constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Api\SearchCriteriaInterface     $searchCriteria
     * @param \Magento\Customer\Model\Session                    $customerSession
     * @param \Aheadworks\StoreLocator\Model\LocationFactory     $locationFactory
     * @param \Dyode\StoreLocator\Model\GeoCoordinateRepository  $geoCoordinateRepository
     * @param \Dyode\ArInvoice\Helper\Data                       $arInvoiceHelper
     * @param \Aheadworks\StoreLocator\Helper\Image              $aheadImageHelper
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SearchCriteriaInterface $searchCriteria,
        Session $customerSession,
        LocationFactory $locationFactory,
        GeoCoordinateRepository $geoCoordinateRepository,
        ArInvoiceHelper $arInvoiceHelper,
        AheadImageHelper $aheadImageHelper
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->searchCriteria = $searchCriteria;
        $this->customerSession = $customerSession;
        $this->locationFactory = $locationFactory;
        $this->arInvoiceHelper = $arInvoiceHelper;
        $this->geoCoordinateRepository = $geoCoordinateRepository;
        $this->aheadImageHelper = $aheadImageHelper;
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
     * then we dont want to show the nearest store.
     *
     * @return bool
     * @throws \Magento\Framework\Exception\LocalizedException
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
     * @return \Dyode\StoreLocator\Api\Data\GeoCoordinateInterface
     * @throws \Magento\Framework\Exception\LocalizedException
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
     * @todo Right now we are loading all locations available. This needs to be changed once
     *       product based store locations are implemented.
     *
     * @return \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection
     */
    public function availableStores()
    {
        if (!$this->productStores) {
            /** @var $storeLocation \Aheadworks\StoreLocator\Model\Location */
            $storeLocation = $this->locationFactory->create();

            $this->productStores = $storeLocation->getCollection()->load();
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
    public function customerZipCode()
    {
        return $this->getCustomer()->getDefaultShippingAddress()->getPostcode();
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
}