<?php
/**
 * Dyode_StoreLocator Magento2 Module.
 *
 * Extending Aheadworks_StoreLocator
 *
 * @package   Dyode
 * @module    Dyode_StoreLocator
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\StoreLocator\Model;

use Dyode\StoreLocator\Api\Data\GeoCoordinateInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate as ResourceGeoCoordinate;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotDeleteException;
use Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate\CollectionFactory as GeoCoordinateCollectionFactory;
use Magento\Framework\Api\SortOrder;
use Dyode\StoreLocator\Api\Data\GeoCoordinateSearchResultsInterfaceFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Dyode\StoreLocator\Api\GeoCoordinateRepositoryInterface;
use Dyode\StoreLocator\Api\Data\GeoCoordinateInterfaceFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Reflection\DataObjectProcessor;

class GeoCoordinateRepository implements GeoCoordinateRepositoryInterface
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Dyode\StoreLocator\Model\GeoCoordinateFactory
     */
    protected $geoCoordinateFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate\CollectionFactory
     */
    protected $geoCoordinateCollectionFactory;

    /**
     * @var \Dyode\StoreLocator\Api\Data\GeoCoordinateSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var \Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate
     */
    protected $resource;

    /**
     * @var \Magento\Framework\Api\DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @var \Dyode\StoreLocator\Api\Data\GeoCoordinateInterfaceFactory
     */
    protected $dataGeoCoordinateFactory;


    /**
     * GeoCoordinateRepository constructor.
     *
     * @param \Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate                   $resource
     * @param \Dyode\StoreLocator\Model\GeoCoordinateFactory                          $geoCoordinateFactory
     * @param \Dyode\StoreLocator\Api\Data\GeoCoordinateInterfaceFactory              $dataGeoCoordinateFactory
     * @param \Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate\CollectionFactory $geoCoordinateCollectionFactory
     * @param \Dyode\StoreLocator\Api\Data\GeoCoordinateSearchResultsInterfaceFactory $searchResultsFactory
     * @param \Magento\Framework\Api\DataObjectHelper                                 $dataObjectHelper
     * @param \Magento\Framework\Reflection\DataObjectProcessor                       $dataObjectProcessor
     * @param \Magento\Store\Model\StoreManagerInterface                              $storeManager
     */
    public function __construct(
        ResourceGeoCoordinate $resource,
        GeoCoordinateFactory $geoCoordinateFactory,
        GeoCoordinateInterfaceFactory $dataGeoCoordinateFactory,
        GeoCoordinateCollectionFactory $geoCoordinateCollectionFactory,
        GeoCoordinateSearchResultsInterfaceFactory $searchResultsFactory,
        DataObjectHelper $dataObjectHelper,
        DataObjectProcessor $dataObjectProcessor,
        StoreManagerInterface $storeManager
    ) {
        $this->resource = $resource;
        $this->geoCoordinateFactory = $geoCoordinateFactory;
        $this->geoCoordinateCollectionFactory = $geoCoordinateCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->dataGeoCoordinateFactory = $dataGeoCoordinateFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function save(
        GeoCoordinateInterface $geoCoordinate
    ) {
        /* if (empty($geoCoordinate->getStoreId())) {
            $storeId = $this->storeManager->getStore()->getId();
            $geoCoordinate->setStoreId($storeId);
        } */
        try {
            $this->resource->save($geoCoordinate);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the geoCoordinate: %1',
                $exception->getMessage()
            ));
        }
        return $geoCoordinate;
    }

    /**
     * {@inheritdoc}
     */
    public function getById($geoCoordinateId)
    {
        $geoCoordinate = $this->geoCoordinateFactory->create();
        $this->resource->load($geoCoordinate, $geoCoordinateId);
        if (!$geoCoordinate->getId()) {
            throw new NoSuchEntityException(__('Sorry, we don\'t ship to this location "%1".', $geoCoordinateId));
        }
        return $geoCoordinate;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(
        SearchCriteriaInterface $criteria
    ) {
        $collection = $this->geoCoordinateCollectionFactory->create();
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                if ($filter->getField() === 'store_id') {
                    $collection->addStoreFilter($filter->getValue(), false);
                    continue;
                }
                $fields[] = $filter->getField();
                $condition = $filter->getConditionType() ?: 'eq';
                $conditions[] = [$condition => $filter->getValue()];
            }
            $collection->addFieldToFilter($fields, $conditions);
        }

        $sortOrders = $criteria->getSortOrders();
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setTotalCount($collection->getSize());
        $searchResults->setItems($collection->getItems());
        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(
        GeoCoordinateInterface $geoCoordinate
    ) {
        try {
            $this->resource->delete($geoCoordinate);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the GeoCoordinate: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($geoCoordinateId)
    {
        return $this->delete($this->getById($geoCoordinateId));
    }
}
