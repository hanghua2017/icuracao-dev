<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Model\Config\Source;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class \Aheadworks\StoreLocator\Model\Config\Source\TopMenu
 */
class TopMenuTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        /** @var topMenu $topMenu */
        $topMenu = Bootstrap::getObjectManager()->get(\Aheadworks\StoreLocator\Model\Config\Source\TopMenu::class);
        $this->assertEquals(
            [
                0 => __('Disabled'),
                1 => __('Enabled (First Item)'),
                2 => __('Enabled (Last Item)'),
            ],
            $topMenu->toOptionArray()
        );
    }
}
