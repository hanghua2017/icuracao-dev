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



namespace Mirasvit\Rma\Test\Unit\Helper;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use Mirasvit\Rma\Model\Config as Config;

/**
 * @covers \Mirasvit\Rma\Helper\Process
 * @SuppressWarnings(PHPMD)
 */
class ProcessTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Helper\Process|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $processHelper;

    /**
     * @var \Mirasvit\Rma\Model\RmaFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaMock;

    /**
     * @var \Magento\Sales\Model\OrderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderFactoryMock;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * @var \Mirasvit\Rma\Model\ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemMock;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderItemFactoryMock;

    /**
     * @var \Magento\Sales\Model\Order\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderItemMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Field\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Field\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Rma\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Rma\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaCollectionMock;

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userCollectionFactoryMock;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userCollectionMock;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerCollectionFactoryMock;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\MstCore\Helper\Attachment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mstcoreAttachmentMock;

    /**
     * @var \Mirasvit\Rma\Helper\Mail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaMailMock;

    /**
     * @var \Mirasvit\Helpdesk\Helper\String|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $helpdeskStringMock;

    /**
     * @var \Mirasvit\Rma\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaDataMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeManagerMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    /**
     * @var \Magento\Backend\Model\Auth|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $authMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
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
            'delete', ],
            [],
            '',
            false
        );
        $this->orderFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->orderMock));
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
        $this->orderItemFactoryMock = $this->getMock(
            '\Magento\Sales\Model\Order\ItemFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->orderItemMock = $this->getMock(
            '\Magento\Sales\Model\Order\Item',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->orderItemFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->orderItemMock));
        $this->ticketFactoryMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\TicketFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ticketMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Ticket',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->ticketFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ticketMock));
        $this->fieldCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Field\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->fieldCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Field\Collection',
            ['load',
            'save',
            'delete',
            'addFieldToFilter',
            'setOrder',
            'getFirstItem',
            'getLastItem', ],
            [],
            '',
            false
        );
        $this->fieldCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->fieldCollectionMock));
        $this->rmaCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Rma\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->rmaCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Rma\Collection',
            ['load',
            'save',
            'delete',
            'addFieldToFilter',
            'setOrder',
            'getFirstItem',
            'getLastItem', ],
            [],
            '',
            false
        );
        $this->rmaCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->rmaCollectionMock));
        $this->userCollectionFactoryMock = $this->getMock(
            '\Magento\User\Model\ResourceModel\User\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->userCollectionMock = $this->getMock(
            '\Magento\User\Model\ResourceModel\User\Collection',
            ['load',
            'save',
            'delete',
            'addFieldToFilter',
            'setOrder',
            'getFirstItem',
            'getLastItem', ],
            [],
            '',
            false
        );
        $this->userCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->userCollectionMock));
        $this->customerCollectionFactoryMock = $this->getMock(
            '\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->customerCollectionMock = $this->getMock(
            '\Magento\Customer\Model\ResourceModel\Customer\Collection',
            ['load',
            'save',
            'delete',
            'addFieldToFilter',
            'setOrder',
            'getFirstItem',
            'getLastItem', ],
            [],
            '',
            false
        );
        $this->customerCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->customerCollectionMock));
        $this->configMock = $this->getMock(
            '\Mirasvit\Rma\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->mstcoreAttachmentMock = $this->getMock(
            '\Mirasvit\MstCore\Helper\Attachment',
            [],
            [],
            '',
            false
        );
        $this->rmaMailMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Mail',
            [],
            [],
            '',
            false
        );
        $this->helpdeskStringMock = $this->getMock(
            '\Mirasvit\Helpdesk\Helper\StringHelper',
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
        $this->storeManagerMock = $this->getMockForAbstractClass(
            '\Magento\Store\Model\StoreManagerInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->localeDateMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Stdlib\DateTime\TimezoneInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->authMock = $this->getMock(
            '\Magento\Backend\Model\Auth',
            [],
            [],
            '',
            false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->processHelper = $this->objectManager->getObject(
            '\Mirasvit\Rma\Helper\Process',
            [
                'rmaFactory' => $this->rmaFactoryMock,
                'orderFactory' => $this->orderFactoryMock,
                'itemFactory' => $this->itemFactoryMock,
                'orderItemFactory' => $this->orderItemFactoryMock,
                'ticketFactory' => $this->ticketFactoryMock,
                'fieldCollectionFactory' => $this->fieldCollectionFactoryMock,
                'rmaCollectionFactory' => $this->rmaCollectionFactoryMock,
                'userCollectionFactory' => $this->userCollectionFactoryMock,
                'customerCollectionFactory' => $this->customerCollectionFactoryMock,
                'config' => $this->configMock,
                'mstcoreAttachment' => $this->mstcoreAttachmentMock,
                'rmaMail' => $this->rmaMailMock,
                'helpdeskString' => $this->helpdeskStringMock,
                'rmaData' => $this->rmaDataMock,
                'context' => $this->contextMock,
                'storeManager' => $this->storeManagerMock,
                'localeDate' => $this->localeDateMock,
                'auth' => $this->authMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->processHelper, $this->processHelper);
    }
}
