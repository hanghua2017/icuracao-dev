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



namespace Mirasvit\Rma\Controller\Guest;

class ListActionTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @covers  Mirasvit\Rma\Controller\Guest\ListAction::execute
     */
    public function testListWrongSessionAction()
    {
        $this->dispatch('rma/guest/list');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     * @covers  Mirasvit\Rma\Controller\Guest\ListAction::execute
     */
    public function testListAction()
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->_objectManager->create('Magento\Sales\Model\Order');
        $order->loadByIncrementId('100000001');

        $logger = $this->getMock('Psr\Log\LoggerInterface', [], [], '', false);
        $session = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\Customer\Model\Session',
            [$logger]
        );
        $session->setRMAGuestOrderId($order->getId());

        $this->dispatch('rma/guest/list');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('Returns for order #100000001', $body);
    }
}
