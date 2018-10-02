<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Controller\Adminhtml\Index;

use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoAppArea adminhtml
 */
class MassStatusTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * Base controller URL
     *
     * @var string
     */
    protected $baseControllerUrl = 'http://localhost/index.php/backend/aw_store_locator/location/index';

    /**
     * The resource used to authorize action
     *
     * @var string
     */
    protected $resource = 'Aheadworks_StoreLocator::location_save';

    /**
     * The uri at which to access the controller
     *
     * @var string
     */
    protected $uri = 'backend/aw_store_locator/location/massStatus';

    protected function tearDown()
    {
        /**
         * Unset messages
         */
        Bootstrap::getObjectManager()->get(\Magento\Backend\Model\Session::class)->getMessages(true);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testMassEnableAction()
    {
        $location = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\Location::class
        );
        $location->load('Test Location 1', 'title');

        $this->getRequest()->setPostValue('selected', [$location->getLocationId()])
            ->setPostValue('status', 1)
            ->setPostValue('namespace', 'location_listing');
        $this->dispatch('backend/aw_store_locator/location/massStatus');
        $this->assertSessionMessages(
            $this->equalTo(['A total of 1 location(s) were enabled.']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringStartsWith($this->baseControllerUrl));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testMassDisableAction()
    {
        $location = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\Location::class
        );
        $location->load('Test Location 1', 'title');

        $this->getRequest()->setPostValue('selected', [$location->getLocationId()])
            ->setPostValue('status', 0)
            ->setPostValue('namespace', 'location_listing');
        $this->dispatch('backend/aw_store_locator/location/massStatus');
        $this->assertSessionMessages(
            $this->equalTo(['A total of 1 location(s) were disabled.']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        $this->assertRedirect($this->stringStartsWith($this->baseControllerUrl));
    }

    /**
     * No location Ids specified
     * @magentoDbIsolation enabled
     */
    public function testMassStatusActionNoLocationIds()
    {
        $this->getRequest()->setPostValue('namespace', 'location_listing');
        $this->dispatch('backend/aw_store_locator/location/massStatus');
        $this->assertSessionMessages(
            $this->equalTo(['Please select item(s).']),
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );
    }
}
