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

namespace Dyode\Catalog\Controller\Product\Stores;

use Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View\StoreAvailability;
use Dyode\StoreLocator\Model\GeoCoordinateRepository;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\UrlInterface;

/**
 * List product stores by the zip-code.
 *
 */
class Listbyzip extends Action
{

    /**
     * @var \Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View\StoreAvailability
     */
    protected $storesViewModel;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * @var \Dyode\StoreLocator\Model\GeoCoordinateRepository
     */
    protected $geoCoordinateRepository;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * Listbyzip constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Dyode\StoreLocator\Model\GeoCoordinateRepository $geoCoordinateRepository
     * @param \Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View\StoreAvailability $storesViewModel
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     */
    public function __construct(
        Context $context,
        GeoCoordinateRepository $geoCoordinateRepository,
        StoreAvailability $storesViewModel,
        ResultFactory $resultFactory,
        UrlInterface $urlBuilder
    ) {
        parent::__construct($context);
        $this->geoCoordinateRepository = $geoCoordinateRepository;
        $this->storesViewModel = $storesViewModel;
        $this->resultFactory = $resultFactory;
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * Main entry point.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $zipCode = $this->getRequest()->getParam('zip_code', false);
        $productId = (int)$this->getRequest()->getParam('product_id', false);

        if ($productId && $zipCode) {
            try {
                $productStores = $this->collectProductStores($productId);
                $sortedStores = $this->sortStoresByZipCode($productStores, $zipCode);
            } catch (\Exception $e) {
                $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
                $result->setData([
                    'message' => __('No stores are available for the given zipcode.'),
                    'type'    => 'error',
                ]);

                return $result;
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
        $storesFilter = $this->storesViewModel->productAvailableStores($productId);

        if (count($storesFilter) === 0) {
            throw new \Exception(__('No stores are available for the given zipcode.'));
        }

        return $this->storesViewModel->availableStores()
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
            $storeLocationGeoCoordinate = $this->storesViewModel->findStoreLocationGeoCoordiante($store);

            if ($storeLocationGeoCoordinate) {
                $storeDistance = round($this->storesViewModel->arInvoiceHelper()->getDistance(
                    $zipGeoCoordinate->getLat(),
                    $zipGeoCoordinate->getLng(),
                    $storeLocationGeoCoordinate->getLat(),
                    $storeLocationGeoCoordinate->getLng()
                ))  ;

                $store->setDistanceFromZip($storeDistance);

                $storeData[$storeDistance] = $store;
            }
        }

        ksort($storeData);

        return array_slice($storeData, 0, 3);
    }

    /**
     * Prepare store data and send it as JSON response.
     *
     * @param array[\Aheadworks\StoreLocator\Model\Location]
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function sendResponse($stores)
    {
        if (!$stores || !is_array($stores) || count($stores) === 0) {
            $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $result->setData([
                'message' => __('No stores are available for the given zipcode.'),
                'type'    => 'error',
            ]);

            return $result;
        }

        $response = [
            'customerZipCode' => $this->getRequest()->getParam('zip_code'),
            'modalTitle'      => __('Available Stores'),
        ];
        foreach ($stores as $distance => $store) {
            $imagePath = $this->storesViewModel->storeLocationImgHelper()->getImagePath($store->getImage());
            $response['stores'][] = [
                'id'      => $store->getLocationId(),
                'title'   => __($store->getTitle()),
                'imgUrl'  => $this->urlBuilder->getUrl($imagePath),
                'miles'   => $store->getDistanceFromZip(),
                'address' => [
                    'title'       => __($store->getTitle()),
                    'street'      => __($store->getStreet()),
                    'city'        => __($store->getCity()),
                    'region_code' => $store->getRegionId(),
                    'zip'         => $store->getZip(),
                    'phone'       => $store->getPhone(),
                ],
            ];
        }

        $result = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData([
            'data' => $response,
            'type' => 'success',
        ]);
        return $result;
    }
}
