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
use Mirasvit\Rma\Model\Config as Config;

/**
 * @covers \Mirasvit\Rma\Model\Rma
 * @SuppressWarnings(PHPMD)
 */
class RmaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaModel;

    /**
     * @var \Magento\Sales\Model\OrderFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderFactoryMock;

    /**
     * @var \Magento\Sales\Model\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderMock;

    /**
     * @var \Magento\Store\Model\StoreFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeFactoryMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

    /**
     * @var \Magento\Customer\Model\CustomerFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerFactoryMock;

    /**
     * @var \Magento\Customer\Model\Customer|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $customerMock;

    /**
     * @var \Mirasvit\Rma\Model\StatusFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketMock;

    /**
     * @var \Magento\Sales\Model\Order\CreditmemoFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCreditmemoFactoryMock;

    /**
     * @var \Magento\Sales\Model\Order\Creditmemo|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCreditmemoMock;

    /**
     * @var \Magento\Directory\Model\CountryFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $countryFactoryMock;

    /**
     * @var \Magento\Directory\Model\Country|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $countryMock;

    /**
     * @var \Mirasvit\Rma\Model\MessageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Message|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageMock;

    /**
     * @var \Magento\User\Model\UserFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userFactoryMock;

    /**
     * @var \Magento\User\Model\User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Item\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Item\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Message\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Message\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Rma\Helper\StringHelper|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaStringMock;

    /**
     * @var \Mirasvit\Rma\Helper\Ruleevent|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaRuleeventMock;

    /**
     * @var \Mirasvit\Rma\Helper\Attachment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaAttachmentMock;

    /**
     * @var \Mirasvit\MstCore\Helper\Attachment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mstcoreAttachmentMock;

    /**
     * @var \Mirasvit\Rma\Helper\Mail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaMailMock;

    /**
     * @var \Mirasvit\Rma\Helper\Process|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaProcessMock;

    /**
     * @var \Mirasvit\Rma\Helper\Locale|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaLocaleMock;

    /**
     * @var \Mirasvit\Rma\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaDataMock;

    /**
     * @var \Magento\Framework\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $urlManagerMock;

    /**
     * @var \Magento\Backend\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendUrlManagerMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

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
        $this->storeFactoryMock = $this->getMock(
            '\Magento\Store\Model\StoreFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->storeMock = $this->getMock(
            '\Magento\Store\Model\Store',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->storeFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->storeMock));
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
        $this->orderCreditmemoFactoryMock = $this->getMock(
            '\Magento\Sales\Model\Order\CreditmemoFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->orderCreditmemoMock = $this->getMock(
            '\Magento\Sales\Model\Order\Creditmemo',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->orderCreditmemoFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->orderCreditmemoMock));
        $this->countryFactoryMock = $this->getMock(
            '\Magento\Directory\Model\CountryFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->countryMock = $this->getMock(
            '\Magento\Directory\Model\Country',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->countryFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->countryMock));
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
        $this->itemCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Item\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->itemCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Item\Collection',
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
        $this->itemCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->itemCollectionMock));
        $this->messageCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Message\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->messageCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Message\Collection',
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
        $this->messageCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->messageCollectionMock));
        $this->configMock = $this->getMock(
            '\Mirasvit\Rma\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->rmaStringMock = $this->getMock(
            '\Mirasvit\Rma\Helper\StringHelper',
            [],
            [],
            '',
            false
        );
        $this->rmaRuleeventMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Ruleevent',
            [],
            [],
            '',
            false
        );
        $this->rmaAttachmentMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Attachment',
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
        $this->rmaProcessMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Process',
            [],
            [],
            '',
            false
        );
        $this->rmaLocaleMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Locale',
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
        $this->urlManagerMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Url',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->backendUrlManagerMock = $this->getMock(
            '\Magento\Backend\Model\Url',
            [],
            [],
            '',
            false
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
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Rma',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Rma\Collection',
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
        $this->rmaModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Rma',
            [
                'orderFactory' => $this->orderFactoryMock,
                'storeFactory' => $this->storeFactoryMock,
                'customerFactory' => $this->customerFactoryMock,
                'statusFactory' => $this->statusFactoryMock,
                'ticketFactory' => $this->ticketFactoryMock,
                'orderCreditmemoFactory' => $this->orderCreditmemoFactoryMock,
                'countryFactory' => $this->countryFactoryMock,
                'messageFactory' => $this->messageFactoryMock,
                'userFactory' => $this->userFactoryMock,
                'itemCollectionFactory' => $this->itemCollectionFactoryMock,
                'messageCollectionFactory' => $this->messageCollectionFactoryMock,
                'config' => $this->configMock,
                'rmaString' => $this->rmaStringMock,
                'rmaRuleevent' => $this->rmaRuleeventMock,
                'rmaAttachment' => $this->rmaAttachmentMock,
                'mstcoreAttachment' => $this->mstcoreAttachmentMock,
                'rmaMail' => $this->rmaMailMock,
                'rmaProcess' => $this->rmaProcessMock,
                'rmaLocale' => $this->rmaLocaleMock,
                'rmaData' => $this->rmaDataMock,
                'urlManager' => $this->urlManagerMock,
                'backendUrlManager' => $this->backendUrlManagerMock,
                'localeDate' => $this->localeDateMock,
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
        $this->assertEquals($this->rmaModel, $this->rmaModel);
    }
}
