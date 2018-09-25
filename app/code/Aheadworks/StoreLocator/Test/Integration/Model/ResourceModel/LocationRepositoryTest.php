<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Model\ResourceModel;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Integration test for service layer \Aheadworks\StoreLocator\Model\ResourceModel\LocationRepository
 */
class LocationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var LocationRepositoryInterface */
    private $repository;

    /** @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    /** @var \Aheadworks\StoreLocator\Model\Data\Location[] */
    private $expectedLocations;

    /** @var \Aheadworks\StoreLocator\Api\Data\LocationInterfaceFactory */
    private $locationFactory;

    /** @var  \Magento\Framework\Api\DataObjectHelper */
    protected $dataObjectHelper;

    /** @var \Aheadworks\StoreLocator\Model\Location */
    private $location;

    protected function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        $this->repository = $this->objectManager
            ->create(\Aheadworks\StoreLocator\Api\LocationRepositoryInterface::class);
        $this->locationFactory = $this->objectManager
            ->create(\Aheadworks\StoreLocator\Api\Data\LocationInterfaceFactory::class);
        $this->dataObjectHelper = $this->objectManager
            ->create(\Magento\Framework\Api\DataObjectHelper::class);
    }

    protected function tearDown()
    {
        $this->getLocationObject();

        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var \Aheadworks\StoreLocator\Model\LocationRegistry $locationRegistry */
        $locationRegistry = $objectManager->get(\Aheadworks\StoreLocator\Model\LocationRegistry::class);
        $locationRegistry->remove($this->location->getId());
    }

    /**
     * @magentoDataFixture  ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoAppIsolation enabled
     */
    public function testSaveLocationChanges()
    {
        $this->getLocationObject();
        $this->getExpectedLocations($this->location->getId());

        $location = $this->repository->getById($this->location->getId());

        $location->setPhone('111' . $location->getPhone());
        $location = $this->repository->save($location);
        $this->assertEquals($this->location->getId(), $location->getLocationId());

        $savedLocation = $this->repository->getById($this->location->getId());
        $this->assertNotEquals($this->expectedLocations[0]->getPhone(), $savedLocation->getPhone());
    }

    /**
     * @magentoDataFixture  ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoAppIsolation enabled
     */
    public function testGetLocationById()
    {
        $this->getLocationObject();
        $this->getExpectedLocations($this->location->getId());

        $location = $this->repository->getById($this->location->getId());
        $this->assertEquals($this->expectedLocations[0], $location);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @expectedException \Magento\Framework\Exception\NoSuchEntityException
     * @expectedExceptionMessage No such entity with locationId = 12345
     */
    public function testGetLocationByIdBadLocationId()
    {
        $this->getLocationObject();

        $this->repository->deleteById($this->location->getId());
        $this->repository->getById(12345);
    }

    /**
     * @magentoDataFixture  ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoAppIsolation enabled
     */
    public function testSaveNewAddress()
    {
        $this->getLocationObject();
        $this->getExpectedLocations($this->location->getId());

        $proposedLocation = $this->createSecondLocation();

        $returnedLocation = $this->repository->save($proposedLocation);
        $this->assertNotNull($returnedLocation->getLocationId());

        $savedLocation = $this->repository->getById($returnedLocation->getLocationId());

        $expectedLocation = $this->expectedLocations[1];
        $expectedLocation->setLocationId($savedLocation->getLocationId());
        $expectedLocation->setCountryId($this->expectedLocations[1]->getCountryId());
        $this->assertEquals($expectedLocation, $savedLocation);
    }

    /**
     * @magentoDataFixture  ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoAppIsolation enabled
     */
    public function testSaveNewInvalidLocation()
    {
        $address = $this->createFirstLocation()
            ->setLocationId(null)
            ->setTitle(null)
            ->setCity(null)
            ->setStreet(null);
        try {
            $this->repository->save($address);
        } catch (InputException $exception) {
            $this->assertEquals(InputException::DEFAULT_MESSAGE, $exception->getMessage());
            $errors = $exception->getErrors();
            $this->assertCount(3, $errors);
            $this->assertEquals('`Title` is a required field.', $errors[0]->getLogMessage());
            $this->assertEquals('`City` is a required field.', $errors[1]->getLogMessage());
            $this->assertEquals('`Street` is a required field.', $errors[2]->getLogMessage());
        }
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testDeleteLocation()
    {
        $this->getLocationObject();

        $locationId = $this->location->getId();

        $locationDataObject = $this->repository->getById($locationId);
        $this->assertEquals($locationDataObject->getLocationId(), $locationId);

        $this->repository->delete($locationDataObject);

        try {
            $locationDataObject = $this->repository->getById($locationId);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (NoSuchEntityException $exception) {
            $this->assertEquals('No such entity with locationId = ' . $locationId, $exception->getMessage());
        }
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testDeleteLocationById()
    {
        $this->getLocationObject();

        $locationId = $this->location->getId();

        $locationDataObject = $this->repository->getById($locationId);
        $this->assertEquals($locationDataObject->getLocationId(), $locationId);

        $this->repository->deleteById($locationId);

        try {
            $locationDataObject = $this->repository->getById($locationId);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (NoSuchEntityException $exception) {
            $this->assertEquals('No such entity with locationId = ' . $locationId, $exception->getMessage());
        }
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testDeleteLocationFromBadLocationId()
    {
        $this->getLocationObject();

        try {
            $this->repository->deleteById($this->location->getId());
            $this->repository->deleteById(12345);
            $this->fail("Expected NoSuchEntityException not caught");
        } catch (NoSuchEntityException $exception) {
            $this->assertEquals('No such entity with locationId = 12345', $exception->getMessage());
        }
    }

    /**
     * @param \Magento\Framework\Api\Filter[] $filters
     * @param \Magento\Framework\Api\Filter[] $filterGroup
     * @param array $expectedResult array of expected results indexed by ID
     *
     * @dataProvider searchLocationDataProvider
     *
     * @magentoDataFixture  ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoDataFixture  ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location_two.php
     * @magentoAppIsolation enabled
     */
    public function testSearchLocation($filters, $filterGroup, $expectedResult)
    {
        /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchBuilder */
        $searchBuilder = $this->objectManager->create(\Magento\Framework\Api\SearchCriteriaBuilder::class);
        foreach ($filters as $filter) {
            $searchBuilder->addFilters([$filter]);
        }

        if ($filterGroup !== null) {
            $searchBuilder->addFilters($filterGroup);
        }

        $searchResults = $this->repository->getList($searchBuilder->create());

        $this->assertEquals(count($expectedResult), $searchResults->getTotalCount());

        /** @var \Aheadworks\StoreLocator\Api\Data\LocationInterface $item */
        foreach ($searchResults->getItems() as $item) {
            $this->assertEquals(
                $expectedResult[$item->getTitle()]['city'],
                $item->getCity()
            );
            $this->assertEquals(
                $expectedResult[$item->getTitle()]['zip'],
                $item->getZip()
            );
            $this->assertEquals(
                $expectedResult[$item->getTitle()]['street'],
                $item->getStreet()
            );
            unset($expectedResult[$item->getTitle()]);
        }
    }

    public function searchLocationDataProvider()
    {
        /**
         * @var \Magento\Framework\Api\FilterBuilder $filterBuilder
         */
        $filterBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create(\Magento\Framework\Api\FilterBuilder::class);
        return [
            'Location with zip 35203' => [
                [$filterBuilder->setField('zip')->setValue('35203')->create()],
                null,
                ['Test Location 1' => ['city' => 'Birmingham', 'zip' => '35203', 'street' => '1200 Street Address']],
            ],
            'Location with city Birmingham' => [
                [$filterBuilder->setField('city')->setValue('Birmingham')->create()],
                null,
                ['Test Location 1' => ['city' => 'Birmingham', 'zip' => '35203', 'street' => '1200 Street Address']],
            ],
            'Locations with street 1200 Street Address' => [
                [$filterBuilder->setField('street')->setValue('1200 Street Address')->create()],
                null,
                [
                    'Test Location 1' => ['city' => 'Birmingham', 'zip' => '35203', 'street' => '1200 Street Address'],
                    'Test Location 2' => ['city' => 'Chicago', 'zip' => '47676', 'street' => '1200 Street Address']
                ],
            ],
            'Locations with zip of either 75477 or 47676' => [
                [],
                [
                    $filterBuilder->setField('zip')->setValue('35203')->create(),
                    $filterBuilder->setField('zip')->setValue('47676')->create()
                ],
                [
                    'Test Location 1' => ['city' => 'Birmingham', 'zip' => '35203', 'street' => '1200 Street Address'],
                    'Test Location 2' => ['city' => 'Chicago', 'zip' => '47676', 'street' => '1200 Street Address']
                ],
            ],
            'Locations with zip greater than 0' => [
                [$filterBuilder->setField('zip')->setValue('0')->setConditionType('gt')->create()],
                null,
                [
                    'Test Location 1' => ['city' => 'Birmingham', 'zip' => '35203', 'street' => '1200 Street Address'],
                    'Test Location 2' => ['city' => 'Chicago', 'zip' => '47676', 'street' => '1200 Street Address']
                ],
            ]
        ];
    }

    /**
     * Helper function that returns an location Data Object that matches the data from first location fixture
     *
     * @return \Aheadworks\StoreLocator\Api\Data\LocationInterface
     */
    private function createFirstLocation()
    {
        $this->getLocationObject();
        $this->getExpectedLocations($this->location->getId());

        $location = $this->locationFactory->create();
        $this->dataObjectHelper->mergeDataObjects(
            \Aheadworks\StoreLocator\Api\Data\LocationInterface::class,
            $location,
            $this->expectedLocations[0]
        );
        $location->setLocationId(null);
        return $location;
    }

    /**
     * Helper function that returns an location Data Object that matches the data from second location fixture
     *
     * @return \Aheadworks\StoreLocator\Api\Data\LocationInterface
     */
    private function createSecondLocation()
    {
        $this->getLocationObject();
        $this->getExpectedLocations($this->location->getId());

        $location = $this->locationFactory->create();
        $this->dataObjectHelper->mergeDataObjects(
            \Aheadworks\StoreLocator\Api\Data\LocationInterface::class,
            $location,
            $this->expectedLocations[1]
        );
        $location->setLocationId(null);
        return $location;
    }

    public function getLocationObject()
    {
        $this->location = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\Location::class
        );
        $this->location->load('Test Location 1', 'title');
    }

    public function getExpectedLocations($locationId)
    {
        $location = $this->locationFactory->create()
            ->setLocationId($locationId)
            ->setTitle('Test Location 1')
            ->setDescription('Description')
            ->setStatus(1)
            ->setSortOrder(1)
            ->setCountryId('US')
            ->setRegionId('1')
            ->setCity('Birmingham')
            ->setStreet('1200 Street Address')
            ->setZip('35203')
            ->setPhone('1-541-754-3010')
            ->setZoom(12)
            ->setLatitude(33.5218)
            ->setLongitude(-86.8112)
            ->setImage('image.png')
            ->setCustomMarker('marker.png')
            ->setStores([0]);

        $location2 = $this->locationFactory->create()
            ->setLocationId('2')
            ->setTitle('Test Location 2')
            ->setDescription('Description')
            ->setStatus(0)
            ->setSortOrder(1)
            ->setCountryId('US')
            ->setRegionId('1')
            ->setCity('Chicago')
            ->setStreet('731 Street Address')
            ->setZip('47676')
            ->setPhone('1-541-754-3010')
            ->setZoom(12)
            ->setLatitude(33.5218)
            ->setLongitude(55.8112)
            ->setImage('image2.png')
            ->setCustomMarker('marker2.png')
            ->setStores([0]);

        $this->expectedLocations = [$location, $location2];
    }
}
