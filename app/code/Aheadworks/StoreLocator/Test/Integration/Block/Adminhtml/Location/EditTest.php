<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Block\Adminhtml;

use Aheadworks\StoreLocator\Controller\RegistryConstants;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class EditTest
 *
 * @magentoAppArea adminhtml
 * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
 */
class EditTest extends \PHPUnit_Framework_TestCase
{
    /**
     * The edit block under test.
     *
     * @var Edit
     */
    private $block;

    /**
     * Core Registry.
     *
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * The location Id.
     *
     * @var int
     */
    private $locationId;

    /**
     * Execute per test initialization.
     */
    public function setUp()
    {
        $objectManager = Bootstrap::getObjectManager();
        $objectManager->get(\Magento\Framework\App\State::class)->setAreaCode('adminhtml');

        $location = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\Location::class
        );
        $this->locationId = $location->load('Test Location 1', 'title')->getLocationId();

        $this->coreRegistry = $objectManager->get(\Magento\Framework\Registry::class);
        $this->coreRegistry->register(RegistryConstants::CURRENT_LOCATION_ID, $this->locationId);

        $this->block = $objectManager->get(
            \Magento\Framework\View\LayoutInterface::class
        )->createBlock(
            \Aheadworks\StoreLocator\Block\Adminhtml\Location\Edit::class,
            '',
            ['coreRegistry' => $this->coreRegistry]
        );
    }

    /**
     * Execute post class cleanup after all tests have executed.
     */
    public function tearDown()
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_LOCATION_ID);
    }

    /**
     * Verify that the correct save and continue Url is generated.
     */
    public function testSaveAndContinueUrl()
    {
        $this->assertContains('aw_store_locator/location/save', $this->block->_getSaveAndContinueUrl());
    }

    /**
     * Verify that the header text is correct for a new location.
     */
    public function testGetHeaderTextNewLocation()
    {
        $this->coreRegistry->unregister(RegistryConstants::CURRENT_LOCATION_ID);
        $this->assertEquals('New Location', $this->block->getHeaderText());
    }

    /**
     * Verify that the header text is correct for an existing location.
     */
    public function testGetHeaderTextExistingLocation()
    {
        $this->assertEquals('Test Location 1', $this->block->getHeaderText());
    }

    /**
     * Verify that the correct location validation Url is generated.
     */
    public function testGetValidationUrl()
    {
        $this->assertContains('aw_store_locator/location/validate', $this->block->getValidationUrl());
    }

    /**
     * Verify the basic content of the block's form Html.
     */
    public function testGetFormHtml()
    {
        $html = $this->block->getFormHtml();
        $this->assertContains('<div class="entry-edit form-inline">', $html);
        $this->assertContains('id="edit_form"', $html);
    }
}
