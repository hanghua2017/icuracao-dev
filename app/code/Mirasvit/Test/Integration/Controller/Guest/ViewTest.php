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

class ViewTest extends \Magento\TestFramework\TestCase\AbstractController
{
    /**
     * @magentoDataFixture Mirasvit/Rma/_files/rma.php
     * @covers  Mirasvit\Rma\Controller\Guest\View::execute
     */
    public function testViewAction()
    {
        $this->getRequest()->setParam('id', '87cb09bf721c860e591568ec93239497');
        $this->dispatch('rma/guest/view');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertContains('RMA #100100001', $body);
    }
}
