<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\ResourceModel\Location;

use Aheadworks\StoreLocator\Model\Config;
use Aheadworks\StoreLocator\Model\Location;
use Aheadworks\StoreLocator\Model\ResourceModel\Location as ResourceModelLocation;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Collection.
 */
class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $locationStoreTable = null;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    protected $_idFieldName = 'location_id';

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param CountryFactory $countryFactory
     * @param RegionFactory $regionFactory
     * @param StoreManagerInterface $storeManager,
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        CountryFactory $countryFactory,
        RegionFactory $regionFactory,
        StoreManagerInterface $storeManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->countryFactory = $countryFactory;
        $this->regionFactory = $regionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(Location::class, ResourceModelLocation::class);
    }

    /**
     * @return string
     */
    protected function getLocationStoreTable()
    {
        if ($this->locationStoreTable === null) {
            $this->locationStoreTable = $this->getTable('aw_storelocator_location_store');
        }
        return $this->locationStoreTable;
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoadData()
    {
        parent::_afterLoadData();

        $this->assignCountryName();
        $this->assignRegionName();

        $this->assignStores();

        return $this;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function assignCountryName()
    {
        foreach ($this->getItems() as $item) {
            $country = $this->countryFactory->create()->loadByCode($item->getCountryId());
            $item->setCountry($country->getName());
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function assignRegionName()
    {
        foreach ($this as $item) {
            $region = $this->regionFactory->create()->load($item->getRegionId());
            $item->setRegion($region->getName());
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function assignStores()
    {
        foreach ($this as $item) {
            $connection = $this->getConnection();

            $select = $connection->select()->from(
                ['store' => $this->getLocationStoreTable()],
                ['store_id']
            )->where(
                'store.location_id IN (?)',
                $item->getLocationId()
            );
            $result = $connection->fetchCol($select);

            if ($result) {
                $item->setStores(implode(',', $result));
            }
        }

        return $this;
    }

    /**
     * @param array $locationIds
     * @return $this
     */
    public function addLocationIdFilter($locationIds)
    {
        if (is_array($locationIds)) {
            if (empty($locationIds)) {
                $condition = '';
            } else {
                $condition = ['in' => $locationIds];
            }
        } elseif (is_numeric($locationIds)) {
            $condition = $locationIds;
        } elseif (is_string($locationIds)) {
            $ids = explode(',', $locationIds);
            if (empty($ids)) {
                $condition = $locationIds;
            } else {
                $condition = ['in' => $ids];
            }
        }
        $this->addFieldToFilter('location_id', $condition);
        return $this;
    }

    /**
     * @param string $radius
     * @param string $measurement
     * @param string $latitude
     * @param string $longitude
     * @return $this
     */
    public function addRadiusFilter($radius, $measurement, $latitude, $longitude)
    {
        if (null === $radius) {
            return $this;
        }

        $radius = (int) $radius;
        if ($radius == 0) {
            return $this;
        }

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        $ratio = $measurement == 'km' ? Config::DEFAULT_KILOMETERS_RATIO : Config::DEFAULT_MILES_RATIO;

        $this->getSelect()
            ->columns('( ' . $ratio . ' * acos( cos( radians(' .
                $latitude .
                ') ) * cos( radians(main_table.latitude) ) * cos( radians(main_table.longitude) - radians(' .
                $longitude .
                ') ) + sin( radians(' .
                $latitude .
                ') ) * sin( radians(main_table.latitude) ) ) ) AS distance')
            ->having('distance < '. $radius);

        return $this;
    }

    /**
     * @param int|int[] $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        $inCond = $this->getConnection()->prepareSqlCondition('store.store_id', ['in' => [0, $storeId]]);
        $this->getSelect()->distinct(true)->join(
            ['store' => $this->getLocationStoreTable()],
            'main_table.location_id=store.location_id',
            []
        );
        $this->getSelect()->where($inCond);

        return $this;
    }

    /**
     * @param string $dir
     * @return $this
     */
    public function orderBySortOrder($dir = self::SORT_ORDER_ASC)
    {
        $this->getSelect()->order('sort_order ' . $dir);
        return $this;
    }
}
