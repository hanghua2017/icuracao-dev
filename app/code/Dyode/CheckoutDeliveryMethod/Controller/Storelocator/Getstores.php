<?php
/**
 * Dyode_CheckoutDeliveryMethod Magento2 Module.
 *
 * Add a new checkout step in checkout
 *
 * @package   Dyode
 * @module    Dyode_CheckoutDeliveryMethod
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\CheckoutDeliveryMethod\Controller\Storelocator;

use Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View\StoreAvailability;
use Dyode\StoreLocator\Model\GeoCoordinateRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Getstores extends Action
{

    /**
     * @var \Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View\StoreAvailability
     */
    protected $storeViewModel;

    /**
     * @var \Dyode\StoreLocator\Model\GeoCoordinateRepository
     */
    protected $geoCoordinateRepository;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * Getstores constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Dyode\StoreLocator\Model\GeoCoordinateRepository $geoCoordinateRepository
     * @param \Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View\StoreAvailability $storeViewModel
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        GeoCoordinateRepository $geoCoordinateRepository,
        StoreAvailability $storeViewModel,
        JsonFactory $resultJsonFactory
    ) {
        $this->geoCoordinateRepository = $geoCoordinateRepository;
        $this->storeViewModel = $storeViewModel;
        $this->resultJsonFactory = $resultJsonFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $zipCode = (int)$this->getRequest()->getParam('zipcode');
        $productId = (int)$this->getRequest()->getParam('pid');

        if ($zipCode || $productId) {
            try {
                $productStores = $this->collectProductStores($productId);
                $sortedStores = $this->sortStoresByZipCode($productStores, $zipCode);

            } catch (\Exception $e) {
                return $this->resultJson()->setData(['error' => __('No stores available')]);
            }

            return $this->sendResponse($sortedStores);
        }
    }

    /**
     * Collect product available store locations
     *
     * @param integer $productId
     * @return \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection
     * @throws \Exception
     */
    public function collectProductStores($productId)
    {
        $storesFilter = $this->storeViewModel->productAvailableStores($productId);

        if (count($storesFilter) === 0) {
            throw new \Exception(__('No stores available'));
        }

        return $this->storeViewModel->availableStores()
            ->addFieldToFilter('store_location_code', ['in' => $storesFilter])
            ->setCurPage(1)
            ->setPageSize(count($storesFilter));
    }

    /**
     * Sort stores basis on the distance b/w the store and the zip-code provided.
     *
     * @param \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection $stores
     * @param string|integer $zipCode
     * @return array $storeData
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function sortStoresByZipCode($stores, $zipCode)
    {
        $zipGeoCoordinate = $this->geoCoordinateRepository->getById($zipCode);

        $storeData = [];
        foreach ($stores as $store) {
            $storeLocationGeoCoordinate = $this->storeViewModel->findStoreLocationGeoCoordiante($store);

            if ($storeLocationGeoCoordinate) {
                $storeDistance = (float)$this->storeViewModel->arInvoiceHelper()->getDistance(
                    $zipGeoCoordinate->getLat(),
                    $zipGeoCoordinate->getLng(),
                    $storeLocationGeoCoordinate->getLat(),
                    $storeLocationGeoCoordinate->getLng()
                );

                $store->setDistanceFromZip($storeDistance);

                $storeData[$storeDistance] = $store;
            }
        }

        ksort($storeData);
        return $storeData;
    }

    /**
     * Prepare store data and send it as JSON response.
     *
     * @param  array[\Aheadworks\StoreLocator\Model\Location] $stores
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function sendResponse($stores)
    {
        if (!$stores || !is_array($stores) || count($stores) === 0) {
            return $this->resultJson()->setData(['error' => __('No stores available')]);
        }

        $response = [];
        foreach ($stores as $distance => $store) {
            $response[] = [
                'id'      => $store->getLocationId(),
                'name'    => $store->getTitle(),
                'image'   => $this->storeViewModel->storeLocationImgHelper()->getImagePath($store->getImage()),
                'miles'   => $distance . 'mi',
                'address' => [
                    'title'       => $store->getTitle(),
                    'street'      => $store->getStreet(),
                    'city'        => $store->getCity(),
                    'region_code' => $store->getRegionId(),
                    'zip'         => $store->getZip(),
                    'phone'       => $store->getPhone(),
                ],
            ];
        }

        return $this->resultJson()->setData($response);
    }

    /**
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function resultJson()
    {
        return $this->resultJsonFactory->create();
    }
}
