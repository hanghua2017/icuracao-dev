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



namespace Mirasvit\Rma\Test\Unit\Model\Item;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Service\Item\Update
 * @codingStandardsIgnoreFile
 */
class UpdateTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\Rma\Service\Item\Update
     */
    protected $service;

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
     * @var \Mirasvit\Rma\Model\ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemMock;

    /**
     * @var \Mirasvit\Rma\Helper\Attachment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaAttachmentMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
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

        $this->itemFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ItemFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->itemMock = $this->getMock(
            '\Mirasvit\Rma\Model\Item',
            ['load',
                'save',
                'delete', ],
            [],
            '',
            false
        );
        $this->itemFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($this->itemMock));

        $this->rmaAttachmentMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Attachment',
            ['saveAttachments', 'getAllowedExtensions', 'getAllowedSize' ],
            [],
            '',
            false
        );

        $this->objectManager = new ObjectManager($this);

        $this->service = $this->objectManager->getObject(
            '\Mirasvit\Rma\Service\Item\Update',
            [
                'itemFactory' => $this->itemFactoryMock,
                'rmaAttachment' => $this->rmaAttachmentMock,
            ]
        );
    }

    /**
     * @covers Mirasvit\Rma\Model\Item\Backend::addItem
     */
    public function testAddItem()
    {
        $itemId = 5;
        $data = [
            [
                'item_id' => $itemId,
            ]
        ];
//        $itemText = 'some item';
//        $rmaId = 5;
//        $userId = 7;
//        $userName = 'John Doe';
//        // set default data
//        $this->rmaMock->method('getId')->willReturn($rmaId);
//        $this->userMock->method('getId')->willReturn($userId);
//        $this->userMock->method('getName')->willReturn($userName);
//
//        // set expectations
//        $this->rmaMock->expects($this->once())->method('setLastReplyName')->with($userName)->willReturnSelf();
//        $this->rmaMock->expects($this->once())->method('save');
//        $this->itemMock->expects($this->once())->method('save');
//        $this->rmaAttachmentMock->expects($this->once())->method('getAllowedExtensions')->willReturn(5);
//        $this->rmaAttachmentMock->expects($this->once())->method('getAllowedSize')->willReturn(15);
//        $this->rmaAttachmentMock->expects($this->once())->method('saveAttachments');

        $this->service->updateItems($this->rmaMock, $data);

//        $this->assertEquals($this->itemMock, $item);
//        $this->assertEquals($rmaId, $item->getRmaId());
//        $this->assertTrue($item->getIsVisibleInFrontend());
//        $this->assertTrue($item->getIsCustomerNotified());
//        $this->assertEquals($userId, $item->getUserId());
    }
}