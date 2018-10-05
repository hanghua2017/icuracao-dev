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



namespace Mirasvit\Rma\Controller\Adminhtml\Status;

/**
 * @magentoAppArea adminhtml
 */
class DeleteTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rma::rma_dictionary_status';
        $this->uri = 'backend/rma/status/delete';
        parent::setUp();
    }

    /**
     * @covers  Mirasvit\Rma\Controller\Adminhtml\Status\Delete::execute
     */
    public function testDeleteAction()
    {
        $this->dispatch('backend/rma/status/delete');
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertTrue($this->getResponse()->isRedirect());
    }
}