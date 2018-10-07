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
class AddTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rma::rma_rma';
        $this->uri = 'backend/rma/rma/add';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rma\Controller\Adminhtml\Rma\Add::execute
     */
    public function testAddAction()
    {
        $this->dispatch('backend/rma/rma/add');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('New RMA', $body);
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     * @covers  Mirasvit\Rma\Controller\Adminhtml\Rma\Add::execute
     */
    public function testAddWithOrderAction()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');

        $this->getRequest()->setParam('order_id', $order->getId());
        $this->dispatch('backend/rma/rma/add');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('New RMA', $body);
        $this->assertContains('General Information', $body);
        $this->assertContains('Product Name', $body);
    }
}
