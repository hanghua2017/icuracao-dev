<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Model\Config\Source;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class \Aheadworks\StoreLocator\Model\Config\Source\Region
 */
class RegionTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        $regionCollection = Bootstrap::getObjectManager()->get(
            \Magento\Directory\Model\ResourceModel\Region\CollectionFactory::class
        )->create()->load();

        foreach ($regionCollection as $region) {
            $options[] = ['label' => $region->getName(), 'value' => $region->getRegionId()];
        }
        array_unshift($options, ['value' => '', 'label' => '']);

        /** @var Region $region */
        $region = Bootstrap::getObjectManager()->get(\Aheadworks\StoreLocator\Model\Config\Source\Region::class);
        $this->assertEquals(
            $options,
            $region->toOptionArray()
        );
    }
}
