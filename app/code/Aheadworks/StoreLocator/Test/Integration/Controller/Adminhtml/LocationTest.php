<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Test\Integration\Controller\Adminhtml\Dir;

use Aheadworks\StoreLocator\Api\LocationManagementInterface;
use Aheadworks\StoreLocator\Api\LocationRepositoryInterface;
use Aheadworks\StoreLocator\Controller\RegistryConstants;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * @magentoAppArea adminhtml
 */
class LocationTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * Base controller URL
     *
     * @var string
     */
    protected $baseControllerUrl;

    /** @var LocationRepositoryInterface */
    protected $locationRepository;

    /** @var LocationManagementInterface */
    protected $locationManagement;

    /** @var \Magento\Framework\Data\Form\FormKey */
    protected $formKey;

    /** @var \Magento\Framework\ObjectManagerInterface */
    private $objectManager;

    /** @var \Aheadworks\StoreLocator\Model\Location */
    private $location;

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
    protected $uri = 'backend/aw_store_locator/location/save';

    protected function setUp()
    {
        parent::setUp();
        $this->baseControllerUrl = 'http://localhost/index.php/backend/aw_store_locator/location/';
        $this->locationRepository = Bootstrap::getObjectManager()->get(
            \Aheadworks\StoreLocator\Api\LocationRepositoryInterface::class
        );
        $this->locationManagement = Bootstrap::getObjectManager()->get(
            \Aheadworks\StoreLocator\Api\LocationManagementInterface::class
        );

        $this->formKey = Bootstrap::getObjectManager()->get(
            \Magento\Framework\Data\Form\FormKey::class
        );

        $this->objectManager = Bootstrap::getObjectManager();

        $this->location = Bootstrap::getObjectManager()->create(
            \Aheadworks\StoreLocator\Model\Location::class
        );
    }

    protected function tearDown()
    {
        /**
         * Unset messages
         */
        $this->objectManager->get(\Magento\Backend\Model\Session::class)->getMessages(true);
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveActionWithEmptyPostData()
    {
        $this->getRequest()->setPostValue([]);
        $this->dispatch('backend/aw_store_locator/location/save');
        $this->assertRedirect($this->stringStartsWith($this->baseControllerUrl));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveActionWithInvalidFormData()
    {
        $post = ['whatever' => ['title' => 'Test Location 1', 'status' => 1]];
        $this->getRequest()->setPostValue($post);
        $this->dispatch('backend/aw_store_locator/location/save');

        $this->assertSessionMessages(
            $this->logicalNot($this->isEmpty()),
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );

        $this->assertRedirect($this->stringStartsWith($this->baseControllerUrl . 'new'));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveActionWithInvalidLocationData()
    {
        $post = [
            'location' => [
                'title' => 'Test Location 1',
                'description' => 'Description',
                'sort_order' => 1,
                'lastname' => 'test lastname',
                'country_id' => 'US',
                'region_id' => '1',
                'city' => 'Birmingham',
                'street' => '1200 Street Address',
                'zip' => '35203',
                'phone' => '1-541-754-3010',
                'zoom' => 12,
            ],
        ];
        $this->getRequest()->setPostValue($post);
        $this->dispatch('backend/aw_store_locator/location/save');

        $this->assertSessionMessages(
            $this->logicalNot($this->isEmpty()),
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );

        $this->assertRedirect($this->stringStartsWith($this->baseControllerUrl . 'new'));
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testSaveActionWithValidLocationData()
    {
        $post = [
            'location' => [
                'title' => 'Test Location 1',
                'description' => 'Description',
                'status' => 1,
                'sort_order' => 1,
                'lastname' => 'test lastname',
                'country_id' => 'US',
                'region_id' => '1',
                'city' => 'Birmingham',
                'street' => '1200 Street Address',
                'zip' => '35203',
                'phone' => '1-541-754-3010',
                'zoom' => 12,
                'latitude' => 33.5218,
                'longitude' => -86.8112,
                'image' => 'image.png',
                'custom_marker' => 'marker.png',
                'stores' => [0],
            ],
        ];
        $this->getRequest()->setPostValue($post);
        $this->getRequest()->setParam('back', '1');

        $this->dispatch('backend/aw_store_locator/location/save');

        $this->assertSessionMessages($this->isEmpty(), \Magento\Framework\Message\MessageInterface::TYPE_ERROR);

        $this->assertSessionMessages(
            $this->logicalNot($this->isEmpty()),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );

        $registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $locationId = $registry->registry(RegistryConstants::CURRENT_LOCATION_ID);
        $location = $this->locationRepository->getById($locationId);
        $this->assertEquals('Test Location 1', $location->getTitle());

        $this->assertRedirect(
            $this->stringStartsWith($this->baseControllerUrl . 'edit/location_id/' . $locationId . '/back/1')
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testSaveActionExistingLocationData()
    {
        $savedLocationId = $this->location->load('Test Location 1', 'title')->getLocationId();

        $post = [
            'location' => [
                'location_id' => $savedLocationId,
                'title' => 'Test Location 2',
                'description' => 'Description',
                'status' => 1,
                'sort_order' => 1,
                'lastname' => 'test lastname',
                'country_id' => 'US',
                'region_id' => '1',
                'city' => 'Birmingham',
                'street' => '1200 Street Address',
                'zip' => '35203',
                'phone' => '1-541-754-3010',
                'zoom' => 12,
                'latitude' => 33.5218,
                'longitude' => -86.8112,
                'image' => 'image.png',
                'custom_marker' => 'marker.png',
                'stores' => [0],
            ],
        ];
        $this->getRequest()->setPostValue($post);
        $this->getRequest()->setParam('location_id', $savedLocationId);
        $this->dispatch('backend/aw_store_locator/location/save');

        $this->assertSessionMessages(
            $this->equalTo(['You saved the location.']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );

        $registry = $this->objectManager->get(\Magento\Framework\Registry::class);
        $locationId = $registry->registry(RegistryConstants::CURRENT_LOCATION_ID);
        $location = $this->locationRepository->getById($locationId);
        $this->assertEquals('Test Location 2', $location->getTitle());

        $this->assertRedirect($this->stringStartsWith($this->baseControllerUrl . 'index/key/'));
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testEditAction()
    {
        $savedLocationId = $this->location->load('Test Location 1', 'title')->getLocationId();

        $this->getRequest()->setParam('location_id', $savedLocationId);
        $this->dispatch('backend/aw_store_locator/location/edit');
        $body = $this->getResponse()->getBody();

        $this->assertContains('<h1 class="page-title">Test Location 1</h1>', $body);
    }

    public function testNewAction()
    {
        $this->dispatch('backend/aw_store_locator/location/edit');
        $body = $this->getResponse()->getBody();

        $this->assertContains('<h1 class="page-title">New Location</h1>', $body);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testDeleteAction()
    {
        $savedLocationId = $this->location->load('Test Location 1', 'title')->getLocationId();

        $this->getRequest()->setParam('location_id', $savedLocationId);
        $this->getRequest()->setParam('form_key', $this->formKey->getFormKey());

        $this->getRequest()->setMethod(\Zend\Http\Request::METHOD_POST);

        $this->dispatch('backend/aw_store_locator/location/delete');
        $this->assertRedirect($this->stringContains('aw_store_locator/location/index'));
        $this->assertSessionMessages(
            $this->equalTo(['You deleted the location.']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testNotExistingLocationDeleteAction()
    {
        $this->getRequest()->setParam('location_id', 12345);
        $this->getRequest()->setParam('form_key', $this->formKey->getFormKey());

        $this->getRequest()->setMethod(\Zend\Http\Request::METHOD_POST);

        $this->dispatch('backend/aw_store_locator/location/delete');
        $this->assertRedirect($this->stringContains('aw_store_locator/location/index'));
        $this->assertSessionMessages(
            $this->equalTo(['No such entity with locationId = 12345']),
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testValidateLocationSuccess()
    {
        $post = [
            'location' => [
                'title' => 'Test Location 1',
                'description' => 'Description',
                'status' => 1,
                'sort_order' => 1,
                'lastname' => 'test lastname',
                'country_id' => 'US',
                'region_id' => '1',
                'city' => 'Birmingham',
                'street' => '1200 Street Address',
                'zip' => '35203',
                'phone' => '1-541-754-3010',
                'zoom' => 12,
                'latitude' => 33.5218,
                'longitude' => -86.8112,
                'image' => 'image.png',
                'custom_marker' => 'marker.png',
                'stores' => [0],
            ],
        ];

        $this->getRequest()->setPostValue($post);
        $this->dispatch('backend/aw_store_locator/location/validate');
        $body = $this->getResponse()->getBody();

        $this->assertSessionMessages($this->isEmpty(), \Magento\Framework\Message\MessageInterface::TYPE_ERROR);

        $this->assertEquals('{"error":0}', $body);
    }

    /**
     * @magentoDataFixture ../../../../app/code/Aheadworks/StoreLocator/Test/Integration/_files/location.php
     */
    public function testValidateLocationFailure()
    {
        $post = [
            'location' => [
                'title' => '',
                'description' => 'Description',
                'status' => '',
                'sort_order' => 1,
                'lastname' => 'test lastname',
                'country_id' => '',
                'region_id' => '1',
                'city' => '',
                'street' => '',
                'zip' => '35203',
                'phone' => '1-541-754-3010',
                'zoom' => 12,
                'latitude' => 33.5218,
                'longitude' => -86.8112,
                'image' => 'image.png',
                'custom_marker' => 'marker.png',
                'stores' => '',
            ],
        ];

        $this->getRequest()->setPostValue($post);
        $this->dispatch('backend/aw_store_locator/location/validate');
        $body = $this->getResponse()->getBody();

        $this->assertContains('{"error":true,"messages":', $body);
        $this->assertContains('`Status` is a required field.', $body);
        $this->assertContains('`Title` is a required field.', $body);
        $this->assertContains('`Country` is a required field.', $body);
        $this->assertContains('`City` is a required field.', $body);
        $this->assertContains('`Street` is a required field.', $body);
        $this->assertContains('`Store View` is a required field.', $body);
    }
}
