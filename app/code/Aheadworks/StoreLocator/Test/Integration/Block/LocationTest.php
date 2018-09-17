<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Block;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class LocationTest
 *
 * @magentoAppArea frontend
 */
class LocationTest extends \PHPUnit_Framework_TestCase
{
    /** @var $block */
    protected $block;

    protected function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(\Magento\Framework\App\State::class)->setAreaCode('frontend');

        $layout = $objectManager->get(
            \Magento\Framework\View\LayoutInterface::class
        );

        $layout->addBlock(\Magento\Framework\View\Element\Text::class, 'content');

        $this->block = $layout->addBlock(
            \Aheadworks\StoreLocator\Block\Location::class,
            'aw_store_locator_location'
        );
        $this->block->setTemplate('Aheadworks_StoreLocator::location.phtml');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlWithoutLocationData()
    {
        /** @var $collection \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection */
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection::class
        );
        $this->block->setCollection($collection);

        $html = $this->block->toHtml();
        $this->assertContains('"locationItems": []', $html);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     * @magentoAppIsolation enabled
     */
    public function testToHtmlWithLocationData()
    {
        /** @var $collection \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection */
        $collection = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection::class
        );
        $this->block->setCollection($collection);

        $html = $this->block->toHtml();
        $this->assertContains('"title":"Test Location 1"', $html);
    }
}
