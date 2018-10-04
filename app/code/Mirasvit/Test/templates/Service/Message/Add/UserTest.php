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



namespace Mirasvit\Rma\Test\Unit\Model\Message;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Service\Message\Add\User
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Mirasvit\Rma\Service\Message\Add\User
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
     * @var \Mirasvit\Rma\Model\MessageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Message|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageMock;

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

        $this->messageFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\MessageFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->messageMock = $this->getMock(
            '\Mirasvit\Rma\Model\Message',
            ['load',
                'save',
                'delete', ],
            [],
            '',
            false
        );
        $this->messageFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($this->messageMock));

        $this->rmaAttachmentMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Attachment',
            ['saveAttachments', 'getAllowedExtensions', 'getAllowedSize' ],
            [],
            '',
            false
        );

        $this->objectManager = new ObjectManager($this);

        $this->service = $this->objectManager->getObject(
            '\Mirasvit\Rma\Service\Message\Add\User',
            [
                'messageFactory' => $this->messageFactoryMock,
                'rmaAttachment' => $this->rmaAttachmentMock,
            ]
        );
    }

    /**
     * @covers Mirasvit\Rma\Model\Message\Backend::addMessage
     */
    public function testAddMessage()
    {
        $messageText = 'some message';
        $rmaId = 5;
        $userId = 7;
        $userName = 'John Doe';
        // set default data
        $this->rmaMock->method('getId')->willReturn($rmaId);
        $this->userMock->method('getId')->willReturn($userId);
        $this->userMock->method('getName')->willReturn($userName);

        // set expectations
        $this->rmaMock->expects($this->once())->method('setLastReplyName')->with($userName)->willReturnSelf();
        $this->rmaMock->expects($this->once())->method('save');
        $this->messageMock->expects($this->once())->method('save');
        $this->rmaAttachmentMock->expects($this->once())->method('getAllowedExtensions')->willReturn(5);
        $this->rmaAttachmentMock->expects($this->once())->method('getAllowedSize')->willReturn(15);
        $this->rmaAttachmentMock->expects($this->once())->method('saveAttachments');

        $message = $this->service->addMessage($this->userMock, $this->rmaMock, $messageText);

        $this->assertEquals($this->messageMock, $message);
        $this->assertEquals($rmaId, $message->getRmaId());
        $this->assertTrue($message->getIsVisibleInFrontend());
        $this->assertTrue($message->getIsCustomerNotified());
        $this->assertEquals($userId, $message->getUserId());
    }
}