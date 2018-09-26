<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Model\ResourceModel\Location\Grid;

use Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection as LocationCollection;
use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Search\AggregationInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Document;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Collection.
 */
class Collection extends LocationCollection implements SearchResultInterface
{
    /**
     * @var AggregationInterface
     */
    protected $aggregations;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param CountryFactory $countryFactory
     * @param RegionFactory $regionFactory
     * @param StoreManagerInterface $storeManager
     * @param mixed|null $mainTable
     * @param mixed $resourceModel
     * @param string $model
     * @param null $connection
     * @param AbstractDb|null $resource
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        CountryFactory $countryFactory,
        RegionFactory $regionFactory,
        StoreManagerInterface $storeManager,
        $mainTable,
        $resourceModel,
        $model = Document::class,
        $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $countryFactory,
            $regionFactory,
            $storeManager,
            $connection,
            $resource
        );
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * {@inheritdoc}
     */
    public function getAggregations()
    {
        return $this->aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function setAggregations($aggregations)
    {
        $this->aggregations = $aggregations;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items = null)
    {
        return $this;
    }
}
