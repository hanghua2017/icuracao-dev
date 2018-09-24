<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model;

use Aheadworks\StoreLocator\Api\Data\LocationInterface;
use Aheadworks\StoreLocator\Api\Data\LocationInterfaceFactory;
use Aheadworks\StoreLocator\Model\ResourceModel\Location as ResourceModelLocation;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Location.
 */
class Location extends AbstractModel
{
    /**
     * @var string
     */
    protected $_eventPrefix = 'location';

    /**
     * @var string
     */
    protected $_eventObject = 'location';

    /**
     * @var array
     */
    protected static $searchFields = ['country_id', 'region_id', 'city', 'zip'];

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var LocationInterfaceFactory
     */
    protected $locationDataFactory;

    /**
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param StoreManagerInterface $storeManager
     * @param ResourceModelLocation $resource
     * @param LocationInterfaceFactory $locationDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        StoreManagerInterface $storeManager,
        ResourceModelLocation $resource,
        LocationInterfaceFactory $locationDataFactory,
        DataObjectHelper $dataObjectHelper,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->storeManager = $storeManager;
        $this->locationDataFactory = $locationDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * {@inheritdoc}
     */
    public function _construct()
    {
        $this->_init(ResourceModelLocation::class);
    }

    /**
     * @return LocationInterface
     */
    public function getDataModel()
    {
        $locationData = $this->getData();
        $locationDataObject = $this->locationDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $locationDataObject,
            $locationData,
            LocationInterface::class
        );

        return $locationDataObject;
    }

    /**
     * Retrieve location collection model populated with data.
     *
     * @param array $filters
     * @return AbstractCollection
     */
    public function getLocationCollectionBySearch(array $filters = [])
    {
        $collection = $this->getCollection();

        if (isset($filters['radius']) && isset($filters['measurement'])) {
            $collection->addRadiusFilter(
                $filters['radius'],
                $filters['measurement'],
                $filters['latitude'],
                $filters['longitude']
            );
        }

        foreach ($filters as $filter => $value) {
            if ($value && in_array($filter, self::$searchFields)) {
                $collection->addFieldToFilter($filter, $value);
            }
        }

        $collection->addStoreFilter($this->storeManager->getStore()->getId());

        $collection->addFieldToFilter('status', 1);

        $collection->orderBySortOrder();

        $collection = $collection->load();

        return $collection;
    }
}
