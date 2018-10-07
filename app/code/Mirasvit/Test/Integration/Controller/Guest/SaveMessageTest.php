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

class SaveMessageTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Mirasvit/Rma/_files/rma.php
     * @covers  Mirasvit\Rma\Controller\Guest\SaveMessage::execute
     */
    public function testSavemessageAction()
    {
        $data = [
            'id' => '87cb09bf721c860e591568ec93239497', 'message' => 'some message'
        ];
        $this->getRequest()->setParams($data);
        $this->dispatch('rma/guest/savemessage');

        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());
        $this->assertSessionMessages(
            $this->equalTo(['Your message was successfully added']),
            \Magento\Framework\Message\MessageInterface::TYPE_SUCCESS
        );
    }
}
