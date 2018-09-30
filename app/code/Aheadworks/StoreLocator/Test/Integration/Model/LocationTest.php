<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Model;

class LocationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Aheadworks\StoreLocator\Model\Location
     */
    protected $locationModel;

    /**
     * @var \Aheadworks\StoreLocator\Api\Data\LocationInterfaceFactory
     */
    protected $locationFactory;

    protected function setUp()
    {
        $this->locationModel = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\Location::class
        );
    }

    /**
     * @dataProvider searchLocationByFiltersDataProvider
     *
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location_two.php
     */
    public function testSearchLocationByFilters($filters, $expectedResult)
    {
        $searchResults = $this->locationModel->getLocationCollectionBySearch($filters);

        $this->assertEquals(count($expectedResult), $searchResults->count());

        /** @var \Aheadworks\StoreLocator\Model\Location $item */
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
                $expectedResult[$item->getTitle()]['country_id'],
                $item->getCountryId()
            );
            unset($expectedResult[$item->getTitle()]);
        }
    }

    public function searchLocationByFiltersDataProvider()
    {
        return [
            'Location with zip 35203' => [
                ['zip' => '35203'],
                ['Test Location 1' => ['city' => 'Birmingham', 'zip' => '35203', 'country_id' => 'US']],
            ],
            'Location with city Birmingham' => [
                ['city' => 'Birmingham'],
                ['Test Location 1' => ['city' => 'Birmingham', 'zip' => '35203', 'country_id' => 'US']],
            ],
            'Locations with country US' => [
                ['country_id' => 'US'],
                [
                    'Test Location 1' => ['city' => 'Birmingham', 'zip' => '35203', 'country_id' => 'US'],
                    'Test Location 2' => ['city' => 'Chicago', 'zip' => '47676', 'country_id' => 'US']
                ],
            ]
        ];
    }
}
