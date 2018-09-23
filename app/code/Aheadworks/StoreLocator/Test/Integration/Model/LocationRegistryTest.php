<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Model;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Test for \Aheadworks\StoreLocator\Model\LocationRegistry
 */
class LocationRegistryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\StoreLocator\Model\LocationRegistry
     */
    protected $model;

    /** @var \Aheadworks\StoreLocator\Model\Location */
    private $location;

    protected function setUp()
    {
        $this->model = Bootstrap::getObjectManager()
            ->create(\Aheadworks\StoreLocator\Model\LocationRegistry::class);

        $this->location = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\Location::class
        );
        $this->location->load('Test Location 1', 'title');
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testRetrieve()
    {
        $location = $this->model->retrieve($this->location->getLocationId());
        $this->assertInstanceOf(\Aheadworks\StoreLocator\Model\Location::class, $location);
        $this->assertEquals($this->location->getLocationId(), $location->getId());
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoAppArea adminhtml
     */
    public function testRetrieveCached()
    {
        $locationBeforeDeletion = $this->model->retrieve($this->location->getLocationId());

        Bootstrap::getObjectManager()
            ->create(\Aheadworks\StoreLocator\Model\Location::class)
            ->load($this->location->getLocationId())->delete();

        $this->assertEquals($locationBeforeDeletion, $this->model->retrieve($this->location->getLocationId()));
    }

    /**
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with locationId = 9999
     */
    public function testRetrieveException()
    {
        $this->model->retrieve(9999);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @magentoAppArea adminhtml
     */
    public function testRemove()
    {
        $location = $this->model->retrieve($this->location->getLocationId());
        $this->assertInstanceOf(\Aheadworks\StoreLocator\Model\Location::class, $location);
        $location->delete();
        $this->model->remove($this->location->getLocationId());
        $this->model->retrieve($this->location->getLocationId());
    }
}
