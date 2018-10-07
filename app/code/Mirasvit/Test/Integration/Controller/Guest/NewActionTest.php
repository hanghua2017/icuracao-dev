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

class NewActionTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @covers  Mirasvit\Rma\Controller\Guest\NewAction::execute
     */
    public function testNewGuestAction()
    {
        $this->dispatch('rma/guest/new');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('Request RMA', $body);
    }

    /**
     * @covers  Mirasvit\Rma\Controller\Guest\NewAction::execute
     */
    public function testNewGuestWrongPostAction()
    {
        $data = [
            'order_increment_id' => '1111',
            'email' => 'customer@example.com',
        ];
        $this->getRequest()->setParams($data);
        $this->dispatch('rma/guest/new');
        $body = $this->getResponse()->getBody();
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('Request RMA', $body);
        $this->assertSessionMessages(
            $this->equalTo(['Wrong Order #, Email']),
            \Magento\Framework\Message\MessageInterface::TYPE_ERROR
        );
    }

    /**
     * @magentoDataFixture Magento/Sales/_files/order_with_customer.php
     * @covers  Mirasvit\Rma\Controller\Guest\NewAction::execute
     */
    public function testNewGuestCorrectPostAction()
    {
        $data = [
            'order_increment_id' => '100000001',
            'email' => 'customer@null.com', //email from order object
        ];
        $this->getRequest()->setParams($data);
        $this->dispatch('rma/guest/new');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertRedirect($this->stringContains('rma/guest/list'));
    }
}
