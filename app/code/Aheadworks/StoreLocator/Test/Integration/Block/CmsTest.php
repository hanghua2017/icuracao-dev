<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Block;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class CmsTest
 *
 * @magentoAppArea frontend
 */
class CmsTest extends \PHPUnit_Framework_TestCase
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
            \Aheadworks\StoreLocator\Block\Cms::class,
            'aw_store_locator_cms'
        );
        $this->block->setTemplate('Aheadworks_StoreLocator::cms.phtml');
    }

    /**
     * @magentoAppIsolation enabled
     */
    public function testToHtmlWithSelectedHeaderBlock()
    {
        $html = $this->block->toHtml();
        $this->assertNotContains('<ul class="footer links">', $html);
    }

    /**
     * @magentoConfigFixture current_store aw_store_locator/general/header_block 1
     */
    public function testToHtmlWithoutSelectedHeaderBlock()
    {
        $html = $this->block->toHtml();
        $this->assertContains('<ul class="footer links">', $html);
    }
}
