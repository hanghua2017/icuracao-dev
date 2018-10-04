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



namespace Mirasvit\Rma\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Model\Message
 * @SuppressWarnings(PHPMD)
 */
class MessageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Message|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageModel;

    /**
     * @var \Mirasvit\Rma\Model\StatusFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusMock;

    /**
     * @var \Magento\User\Model\UserFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userFactoryMock;

    /**
     * @var \Magento\User\Model\User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userMock;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Mirasvit\MstCore\Helper\Attachment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mstcoreAttachmentMock;

    /**
     * @var \Mirasvit\Rma\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaDataMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\Model\ResourceModel\AbstractResource|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceMock;

    /**
     * @var \Magento\Framework\Data\Collection\AbstractDb|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resourceCollectionMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->statusFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\StatusFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->statusMock = $this->getMock(
            '\Mirasvit\Rma\Model\Status',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->statusFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->statusMock));
        $this->userFactoryMock = $this->getMock(
            '\Magento\User\Model\UserFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->userMock = $this->getMock(
            '\Magento\User\Model\User',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->userFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->userMock));
        $this->customerFactoryMock = $this->getMock(
            '\Magento\Customer\Model\CustomerFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->customerMock = $this->getMock(
            '\Magento\Customer\Model\Customer',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->customerFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->customerMock));
        $this->mstcoreAttachmentMock = $this->getMock(
            '\Mirasvit\MstCore\Helper\Attachment',
            [],
            [],
            '',
            false
        );
        $this->rmaDataMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Data',
            [],
            [],
            '',
            false
        );
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Message',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Message\Collection',
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
        $this->messageModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Message',
            [
                'statusFactory' => $this->statusFactoryMock,
                'userFactory' => $this->userFactoryMock,
                'customerFactory' => $this->customerFactoryMock,
                'mstcoreAttachment' => $this->mstcoreAttachmentMock,
                'rmaData' => $this->rmaDataMock,
                'context' => $this->contextMock,
                'registry' => $this->registryMock,
                'resource' => $this->resourceMock,
                'resourceCollection' => $this->resourceCollectionMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->messageModel, $this->messageModel);
    }
}
