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



namespace Mirasvit\Rma\Test\Unit\Model\Service;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Rma\Model\Config as Config;

/**
 * @covers \Mirasvit\Rma\Model\Service\Order
 * @SuppressWarnings(PHPMD)
 */
class OrderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Service\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderModel;

    /**
     * @var \Magento\Sales\Model\Convert\OrderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $convertOrderFactoryMock;

    /**
     * @var \Magento\Sales\Model\Convert\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $convertOrderMock;

    /**
     * @var \Magento\Tax\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Framework\Locale\FormatInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeFormatMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->convertOrderFactoryMock = $this->getMock(
            '\Magento\Sales\Model\Convert\OrderFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->convertOrderMock = $this->getMock(
            '\Magento\Sales\Model\Convert\Order',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->convertOrderFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->convertOrderMock));
        $this->configMock = $this->getMock(
            '\Magento\Tax\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->localeFormatMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Locale\FormatInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->orderModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Service\Order',
            [
                'convertOrderFactory' => $this->convertOrderFactoryMock,
                'config' => $this->configMock,
                'localeFormat' => $this->localeFormatMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->orderModel, $this->orderModel);
    }
}
