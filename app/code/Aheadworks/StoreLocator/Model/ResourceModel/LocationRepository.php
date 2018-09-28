<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\ResourceModel;

use Aheadworks\StoreLocator\Api\Data\LocationInterface;
use Aheadworks\StoreLocator\Api\Data\LocationSearchResultsInterfaceFactory;
use Aheadworks\StoreLocator\Api\LocationRepositoryInterface;
use Aheadworks\StoreLocator\Model\LocationFactory;
use Aheadworks\StoreLocator\Model\LocationRegistry;
use Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection;
use Magento\Framework\Api\ExtensibleDataObjectConverter;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Reflection\DataObjectProcessor;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class LocationRepository.
 */
class LocationRepository implements LocationRepositoryInterface
{
    /**
     * @var LocationFactory
     */
    protected $locationFactory;

    /**
     * @var LocationRegistry
     */
    protected $locationRegistry;

    /**
     * @var Location
     */
    protected $locationResourceModel;

    /**
     * @var LocationSearchResultsInterfaceFactory
     */
    protected $searchResultsFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var ExtensibleDataObjectConverter
     */
    protected $extensibleDataObjectConverter;

    /**
     * @var DataObjectProcessor
     */
    private $dataObjectProcessor;

    /**
     * @param LocationFactory $locationFactory
     * @param LocationRegistry $locationRegistry
     * @param Location $locationResourceModel
     * @param LocationSearchResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     * @param ExtensibleDataObjectConverter $extensibleDataObjectConverter
     * @param DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        LocationFactory $locationFactory,
        LocationRegistry $locationRegistry,
        Location $locationResourceModel,
        LocationSearchResultsInterfaceFactory $searchResultsFactory,
        StoreManagerInterface $storeManager,
        ExtensibleDataObjectConverter $extensibleDataObjectConverter,
        DataObjectProcessor $dataObjectProcessor
    ) {
        $this->locationFactory = $locationFactory;
        $this->locationRegistry = $locationRegistry;
        $this->locationResourceModel = $locationResourceModel;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->storeManager = $storeManager;
        $this->extensibleDataObjectConverter = $extensibleDataObjectConverter;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(LocationInterface $location)
    {
        $this->validate($location);

        $locationData = $this->extensibleDataObjectConverter->toNestedArray(
            $location,
            [],
            LocationInterface::class
        );

        $locationModel = $this->locationFactory->create(['data' => $locationData]);
        $locationModel->setId($location->getLocationId());

        $this->locationResourceModel->save($locationModel);
        $this->locationRegistry->push($locationModel);
        $locationId = $locationModel->getId();

        $savedLocation = $this->get($locationId);

        return $savedLocation;
    }

    /**
     * {@inheritdoc}
     */
    public function get($locationId)
    {
        $locationModel = $this->locationRegistry->retrieve($locationId);
        return $locationModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getById($locationId)
    {
        $locationModel = $this->locationRegistry->retrieve($locationId);
        return $locationModel->getDataModel();
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);

        /** @var \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection $collection */
        $collection = $this->locationFactory->create()->getCollection();

        foreach ($searchCriteria->getFilterGroups() as $group) {
            $this->addFilterGroupToCollection($group, $collection);
        }
        $searchResults->setTotalCount($collection->getSize());
        $sortOrders = $searchCriteria->getSortOrders();
        if ($sortOrders) {
            foreach ($searchCriteria->getSortOrders() as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == SortOrder::SORT_ASC) ? 'ASC' : 'DESC'
                );
            }
        }
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $collection->setPageSize($searchCriteria->getPageSize());
        $locations = [];

        /** @var \Aheadworks\StoreLocator\Model\Location $locationModel */
        foreach ($collection as $locationModel) {
            $locations[] = $locationModel->getDataModel();
        }

        $searchResults->setItems($locations);

        return $searchResults;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(LocationInterface $location)
    {
        return $this->deleteById($location->getLocationId());
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($locationId)
    {
        $locationModel = $this->locationRegistry->retrieve($locationId);
        $locationModel->delete();
        $this->locationRegistry->remove($locationId);
        return true;
    }

    /**
     * @param LocationInterface $location
     * @throws InputException
     * @return void
     * @throws \Zend_Validate_Exception
     */
    private function validate(LocationInterface $location)
    {
        $exception = new InputException();

        if (!\Zend_Validate::is(trim($location->getStatus()), 'NotEmpty')) {
            $exception->addError(InputException::requiredField('`Status`'));
        }

        if (!\Zend_Validate::is(trim($location->getTitle()), 'NotEmpty')) {
            $exception->addError(InputException::requiredField('`Title`'));
        }

        if (!\Zend_Validate::is(trim($location->getCountryId()), 'NotEmpty')) {
            $exception->addError(InputException::requiredField('`Country`'));
        }

        if (!\Zend_Validate::is(trim($location->getCity()), 'NotEmpty')) {
            $exception->addError(InputException::requiredField('`City`'));
        }

        if (!\Zend_Validate::is(trim($location->getStreet()), 'NotEmpty')) {
            $exception->addError(InputException::requiredField('`Street`'));
        }

        if (!\Zend_Validate::is($location->getStores(), 'NotEmpty')) {
            $exception->addError(InputException::requiredField('`Store View`'));
        }

        if ($exception->wasErrorAdded()) {
            throw $exception;
        }
    }

    /**
     * @param FilterGroup $filterGroup
     * @param Collection $collection
     * @return void
     */
    protected function addFilterGroupToCollection(
        FilterGroup $filterGroup,
        Collection $collection
    ) {
        $fields = [];
        $conditions = [];
        foreach ($filterGroup->getFilters() as $filter) {
            $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
            $fields[] = $filter->getField();
            $conditions[] = [$condition => $filter->getValue()];
        }
        if ($fields) {
            $collection->addFieldToFilter($fields, $conditions);
        }
    }
}
