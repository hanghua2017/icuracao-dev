<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Model\Config\Source;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class \Aheadworks\StoreLocator\Model\Config\Source\Status
 */
class StatusTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        /** @var Status $status */
        $status = Bootstrap::getObjectManager()->get(\Aheadworks\StoreLocator\Model\Config\Source\Status::class);
        $this->assertEquals(
            [
                ['value' => 1, 'label' => __('Enabled')],
                ['value' => 0, 'label' => __('Disabled')],
            ],
            $status->toOptionArray()
        );
    }
}
