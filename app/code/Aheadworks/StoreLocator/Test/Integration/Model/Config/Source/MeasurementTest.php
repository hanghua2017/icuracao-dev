<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Model\Config\Source;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class \Aheadworks\StoreLocator\Model\Config\Source\Measurement
 */
class MeasurementTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        /** @var Measurement $measurement */
        $measurement = Bootstrap::getObjectManager()->get(
            \Aheadworks\StoreLocator\Model\Config\Source\Measurement::class
        );
        $this->assertEquals(
            [
                'km' => __('kilometers'),
                'mi' => __('miles'),
            ],
            $measurement->toOptionArray()
        );
    }
}
