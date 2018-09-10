<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Model\ResourceModel\Location;

use Magento\TestFramework\Helper\Bootstrap;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection
     */
    protected $collection;

    /** @var \Aheadworks\StoreLocator\Api\Data\LocationInterfaceFactory */
    private $locationFactory;

    /** @var \Aheadworks\StoreLocator\Model\Location */
    private $location;

    /** @var \Aheadworks\StoreLocator\Model\Location */
    private $location2;

    public function setUp()
    {
        $this->collection = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection::class
        );

        $this->locationFactory = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Api\Data\LocationInterfaceFactory::class
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location_two.php
     */
    public function testAddStoreFilter()
    {
        $collectionModel = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection::class
        );
        $this->assertEquals(1, $collectionModel->addStoreFilter(0)->count());

        $collectionModel = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection::class
        );
        $this->assertEquals(2, $collectionModel->addStoreFilter(1)->count());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location_two.php
     */
    public function testLocationIdFilter()
    {
        $this->getLocationObjects();

        $collectionModel = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection::class
        );
        $this->assertEquals(1, $collectionModel->addLocationIdFilter($this->location->getId())->count());

        $collectionModel = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection::class
        );
        $this->assertEquals(2, $collectionModel->addLocationIdFilter(
            [
                $this->location->getId(),
                $this->location2->getId(),
            ]
        )->count());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location_two.php
     */
    public function testAddRadiusFilter()
    {
        $collectionModel = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection::class
        );
        $this->assertEquals(2, $collectionModel->addRadiusFilter(
            'Everywhere',
            'km',
            '55',
            '55'
        )->count());

        $collectionModel = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection::class
        );
        $this->assertEquals(1, $collectionModel->addRadiusFilter(
            '50',
            'mi',
            '34',
            '56'
        )->count());
    }

    public function testOrderBySortOrder()
    {
        $select = $this->collection->getSelect();
        $this->assertEmpty($select->getPart(\Magento\Framework\DB\Select::ORDER));
        $this->collection->orderBySortOrder();
        $this->assertEquals([['sort_order', 'ASC']], $select->getPart(\Magento\Framework\DB\Select::ORDER));
    }

    public function getLocationObjects()
    {
        $this->location = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\Location::class
        );
        $this->location->load('Test Location 1', 'title');

        $this->location2 = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\Location::class
        );
        $this->location2->load('Test Location 2', 'title');
    }
}
