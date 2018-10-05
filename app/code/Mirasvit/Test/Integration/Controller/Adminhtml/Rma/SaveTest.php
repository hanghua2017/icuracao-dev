<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-rma
 * @version   2.0.25
 * @copyright Copyright (C) 2018 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Rma\Controller\Adminhtml\Rma;

/**
 * @magentoAppArea adminhtml
 */
class SaveTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rma::rma_rma';
        $this->uri = 'backend/rma/rma/save';
        parent::setUp();
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     * @covers  Mirasvit\Rma\Controller\Adminhtml\Rma\Save::execute
     */
    public function testSaveNewAction()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');

        $items = $order->getItems();
        $item = array_pop($items);

        $data = [
            'rma_id' => '',
            'order_id' => $order->getId(),
            'increment_id' => '',
            'user_id' => '0',
            'status_id' => 1,
            'firstname' => 'sdf',
            'lastname' => 'asdf',
            'company' => '',
            'telephone' => '3434',
            'email' => 'dva@mirasvit.com.ua',
            'street' => 'asdf',
            'street2' => '',
            'city' => 'asdf',
            'postcode' => '34',
            'items' => [
                $item->getProductId() => [
                'item_id' => '',
                    'order_item_id' => $item->getId(),
                    'qty_requested' => 1,
                    'reason_id' => 2,
                    'condition_id' => 3,
                    'resolution_id' => 3,
                ],
            ],
        ];

        $this->getRequest()->setParams($data);
        $this->dispatch('backend/rma/rma/save');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertSessionMessages(
            $this->equalTo(['RMA was successfully saved']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }

    /**
     * @magentoDataFixture Mirasvit/Rma/_files/rma.php
     * @covers  Mirasvit\Rma\Controller\Adminhtml\Rma\Save::execute
     */
    public function testSaveAction()
    {
        $this->markTestIncomplete('FIX ME PLZ');
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');

        $items = $order->getItems();
        $item = array_pop($items);
        $text = 'some message'.rand();
        $data = [
            'rma_id' => 1,
            'order_id' => $order->getId(),
            'increment_id' => '',
            'user_id' => '0',
            'status_id' => 1,
            'firstname' => 'sdf',
            'lastname' => 'asdf',
            'company' => '',
            'telephone' => '3434',
            'email' => 'dva@mirasvit.com.ua',
            'street' => 'asdf',
            'street2' => '',
            'city' => 'asdf',
            'postcode' => '34',
            'items' => [
                $item->getProductId() => [
                    'item_id' => '',
                    'order_item_id' => $item->getId(),
                    'qty_requested' => 1,
                    'reason_id' => 2,
                    'condition_id' => 3,
                    'resolution_id' => 3,
                ],
            ],
            'reply_type' => 'public',
            'reply' => $text,
        ];

        $this->getRequest()->setParams($data);
        $this->dispatch('backend/rma/rma/save');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertSessionMessages(
            $this->equalTo(['RMA was successfully saved']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
        /* @var \Mirasvit\Rma\Model\Message $message */
        $messages = $this->_objectManager->create('Mirasvit\Rma\Model\Message')->getCollection()
            ->addFieldToFilter('text', $text);
        $this->assertEquals(1, $messages->count());
    }
}
