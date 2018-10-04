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



namespace Mirasvit\Rma\Test\Unit\Model\Config\Source\Order;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Model\Config\Source\Order\Status
 * @SuppressWarnings(PHPMD)
 */
class StatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Config\Source\Order\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusModel;

    /**
     * @var \Magento\Sales\Model\Order\ConfigFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderConfigFactoryMock;

    /**
     * @var \Magento\Sales\Model\Order\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderConfigMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->orderConfigFactoryMock = $this->getMock(
            '\Magento\Sales\Model\Order\ConfigFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->orderConfigMock = $this->getMock(
            '\Magento\Sales\Model\Order\Config',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->orderConfigFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->orderConfigMock));
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->statusModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Config\Source\Order\Status',
            [
                'orderConfigFactory' => $this->orderConfigFactoryMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->statusModel, $this->statusModel);
    }
}
