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



namespace Mirasvit\Rma\Controller\Adminhtml\Report\Rma\Product;

/**
 * @magentoAppArea adminhtml
 */
class ExportExcelTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    /**
     * setUp.
     */
    public function setUp()
    {
        $this->resource = 'Mirasvit_Rma::rma_product';
        $this->uri = 'backend/rma/product/exportexcel';
        parent::setUp();
        $this->markTestSkipped();
    }

    /**
     * @covers  Mirasvit\Rma\Controller\Adminhtml\Report\Rma\Product\ExportExcel::execute
     */
    public function testExportExcelAction()
    {
        $this->dispatch('backend/rma/product/exportexcel');
        $body = $this->getResponse()->getBody();
        $this->assertNotEmpty($body);
        $this->assertNotEquals('noroute', $this->getRequest()->getControllerName());
        $this->assertFalse($this->getResponse()->isRedirect());
    }
}
