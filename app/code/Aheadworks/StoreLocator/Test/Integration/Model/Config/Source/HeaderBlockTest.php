<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Model\Config\Source;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class \Aheadworks\StoreLocator\Model\Config\Source\HeaderBlock
 */
class HeaderBlockTest extends \PHPUnit_Framework_TestCase
{
    public function testToOptionArray()
    {
        $headerBlockCollection = Bootstrap::getObjectManager()->get(
            \Magento\Cms\Model\ResourceModel\Block\CollectionFactory::class
        )->create()->load();

        $options = $headerBlockCollection->toOptionArray();
        array_unshift($options, ['value' => '', 'label' => __('Please select a static block.')]);

        /** @var HeaderBlock $headerBlock */
        $headerBlock = Bootstrap::getObjectManager()->get(
            \Aheadworks\StoreLocator\Model\Config\Source\HeaderBlock::class
        );
        $this->assertEquals(
            $options,
            $headerBlock->toOptionArray()
        );
    }
}
