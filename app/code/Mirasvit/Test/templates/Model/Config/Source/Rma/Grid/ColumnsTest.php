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



namespace Mirasvit\Rma\Test\Unit\Model\Config\Source\Rma\Grid;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Model\Config\Source\Rma\Grid\Columns
 * @SuppressWarnings(PHPMD)
 */
class ColumnsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Config\Source\Rma\Grid\Columns|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $columnsModel;

    /**
     * @var \Mirasvit\Rma\Helper\Field|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaFieldMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->rmaFieldMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Field',
            [],
            [],
            '',
            false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->columnsModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Config\Source\Rma\Grid\Columns',
            [
                'rmaField' => $this->rmaFieldMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->columnsModel, $this->columnsModel);
    }
}
