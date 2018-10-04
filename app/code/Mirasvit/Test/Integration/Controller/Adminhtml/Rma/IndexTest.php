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
class IndexTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rma::rma_rma';
        $this->uri = 'backend/rma/rma/index';
        parent::setUp();
    }

    /**
     * @covers Mirasvit\Rma\Controller\Adminhtml\Ticket\Index::execute
     */
    public function testSuccess()
    {
        $this->dispatch('backend/rma/rma/index');
        $this->assertFalse($this->getResponse()->isRedirect());
        $this->assertSessionMessages($this->isEmpty(), \Magento\Framework\Message\MessageInterface::TYPE_ERROR);
        $this->assertContains(
            '<h1 class="page-title">RMA</h1>',
            $this->getResponse()->getBody()
        );
    }
}
