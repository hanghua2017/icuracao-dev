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



namespace Mirasvit\Rma\Test\Unit\Model\Rma;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Service\Rma\Save\Update
 */
class BackendServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\Rma\Service\Rma\Save\Update
     */
    protected $backendService;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaMock;

    /**
     * @var \Magento\User\Model\User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userMock;

    /**
     * @var \Mirasvit\Rma\Model\RmaFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaFactoryMock;


    /**
     * @var \Mirasvit\Rma\Helper\Attachment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaAttachmentMock;

    /**
     * @var \Magento\Sales\Model\OrderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderFactoryMock;


    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->orderFactoryMock = $this->getMock(
            '\Magento\Sales\Model\OrderFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->orderMock = $this->getMock(
            '\Magento\Sales\Model\Order',
            ['load',
                'save',
                'delete', 'getCustomerId', 'getStoreId'],
            [],
            '',
            false
        );
        $this->orderFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($this->orderMock));

        $this->rmaMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rma',
            ['setLastReplyName', 'save', 'getId'],
            [],
            '',
            false
        );

        $this->userMock = $this->getMock(
            '\Magento\User\Model\User',
            ['load', 'save', 'delete', 'getName', 'getId' ],
            [],
            '',
            false
        );

        $this->rmaFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\RmaFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->rmaMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rma',
            ['load',
                'save',
                'delete', ],
            [],
            '',
            false
        );
        $this->rmaFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($this->rmaMock));

        $this->rmaAttachmentMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Attachment',
            ['saveAttachment', 'getAllowedExtensions', 'getAllowedSize' ],
            [],
            '',
            false
        );

        $this->objectManager = new ObjectManager($this);

        $this->backendService = $this->objectManager->getObject(
            '\Mirasvit\Rma\Service\Rma\Save\Update',
            [
                'orderFactory' => $this->orderFactoryMock,
                'rmaAttachment' => $this->rmaAttachmentMock,
            ]
        );
    }

    /**
     * @covers Mirasvit\Rma\Model\Rma\BackendService::updateRma
     */
    public function testUpdateRma()
    {
        $orderId = 5;
        $customerId = 7;
        $storeId = 10;
        $userId = 15;
        $data = [
            'order_id' => $orderId,
            'street' => 'address',
            'street2' => 'address 2',
            'custom_field' => 'some data'
        ];
        // set default data
        $this->orderMock->method('getCustomerId')->willReturn($customerId);
        $this->orderMock->method('getStoreId')->willReturn($storeId);
        $this->userMock->method('getId')->willReturn($userId);

        // set expectations
        $this->orderMock->expects($this->once())->method('load')->with($orderId)->willReturnSelf();
        $this->rmaMock->expects($this->once())->method('save');
        $this->rmaAttachmentMock->expects($this->once())->method('saveAttachment');

        $rma = $this->backendService->updateRma($this->userMock, $this->rmaMock, $data);

        $this->assertEquals($this->rmaMock, $rma);
        $this->assertEquals($customerId, $rma->getCustomerId());
        $this->assertEquals($storeId, $rma->getStoreId());
        $this->assertEquals($userId, $rma->getUserId());
        $this->assertEquals('some data', $rma->getCustomField());
        $this->assertEquals("address\naddress 2", $rma->getStreet());
    }
}