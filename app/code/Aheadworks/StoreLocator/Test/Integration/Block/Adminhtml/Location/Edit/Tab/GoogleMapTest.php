<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Block\Adminhtml\Edit\Tab;

/**
 * Aheadworks\StoreLocator\Block\Adminhtml\Edit\Tab\General
 *
 * @magentoAppArea adminhtml
 */
class GoogleMapTest extends \PHPUnit_Framework_TestCase
{
    /** @var  \Magento\Backend\Block\Template\Context */
    private $context;

    /** @var  \Magento\Framework\Registry */
    private $coreRegistry;

    /** @var \Aheadworks\StoreLocator\Model\Location */
    private $location;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    private $storeManager;

    /** @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    /** @var  View */
    private $block;

    public function setUp()
    {
        $this->objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

        $this->storeManager = $this->objectManager->get(\Magento\Store\Model\StoreManager::class);
        $this->context = $this->objectManager->get(
            \Magento\Backend\Block\Template\Context::class,
            ['storeManager' => $this->storeManager]
        );

        $this->location = $this->objectManager->get(\Aheadworks\StoreLocator\Model\Location::class);
        $this->location->load('Test Location 1', 'title');

        $this->coreRegistry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $this->coreRegistry->register('aheadworks_location', $this->location);

        $this->block = $this->objectManager->get(
            \Magento\Framework\View\LayoutInterface::class
        )->createBlock(
            \Aheadworks\StoreLocator\Block\Adminhtml\Location\Edit\Tab\GoogleMap::class,
            '',
            [
                'context' => $this->context,
                'registry' => $this->coreRegistry
            ]
        );
    }

    public function tearDown()
    {
        $this->coreRegistry->unregister('aheadworks_location');
    }

    public function testGetTabLabel()
    {
        $this->assertEquals(__('Google Maps Settings'), $this->block->getTabLabel());
    }

    public function testGetTabTitle()
    {
        $this->assertEquals(__('Google Maps Settings'), $this->block->getTabTitle());
    }

    public function testCanShowTab()
    {
        $this->assertTrue($this->block->canShowTab());
    }

    public function testIsHiddenNot()
    {
        $this->assertFalse($this->block->isHidden());
    }

    public function testGetHtml()
    {
        $html = $this->block->toHtml();
        $this->assertContains("<input id=\"location_zoom\"", $html);
        $this->assertContains("<input id=\"location_latitude\"", $html);
        $this->assertContains("<input id=\"location_longitude\"", $html);
        $this->assertContains("<button id=\"find-store-button\"", $html);
        $this->assertContains("<div id=\"google-maps-content\"", $html);
    }
}
