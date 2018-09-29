<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Block;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class SearchTest
 *
 * @magentoAppArea frontend
 */
class SearchTest extends \PHPUnit_Framework_TestCase
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
            \Aheadworks\StoreLocator\Block\Search::class,
            'aw_store_locator_search'
        );
        $this->block->setTemplate('Aheadworks_StoreLocator::search.phtml');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlWithoutSearchData()
    {
        $html = $this->block->toHtml();
        $this->assertContains('name="search[street]"', $html);
        $this->assertContains('name="search[radius]"', $html);
        $this->assertContains('name="search[measurement]"', $html);
        $this->assertContains('name="search[country_id]"', $html);
        $this->assertContains('name="search[region_id]"', $html);
        $this->assertContains('name="search[city]"', $html);
        $this->assertContains('name="search[zip]"', $html);
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlWithSearchData()
    {
        $searchData = [
            'street' => '',
            'radius' => '',
            'measurement' => '',
            'country_id' => '',
            'region_id' => '',
            'city' => 'Birmingham',
            'zip' => '',
        ];

        $this->block->setData($searchData);

        $html = $this->block->toHtml();
        $this->assertRegExp('/<input[^>]*value=\"Birmingham\"[^>]*>/', $html);
    }
}
