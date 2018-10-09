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
 * @covers \Mirasvit\Rma\Helper\Data
 * @SuppressWarnings(PHPMD)
 */
class DataTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dataHelper;

    /**
     * @var \Magento\Store\Model\StoreFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeFactoryMock;

    /**
     * @var \Magento\Store\Model\Store|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeMock;

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
     * @var \Mirasvit\Rma\Model\AttachmentFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentFactoryMock;

    /**
     * @var \Mirasvit\MstCore\Model\Attachment|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $attachmentMock;

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userCollectionFactoryMock;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userCollectionMock;

    /**
     * @var \Magento\Store\Model\ResourceModel\Store\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeCollectionFactoryMock;

    /**
     * @var \Magento\Store\Model\ResourceModel\Store\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $storeCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Rma\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Rma\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Item\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Item\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Status\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Status\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusCollectionMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionFactoryMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Resolution\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolutionCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Resolution\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolutionCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $dateMock;

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
     * setup tests.
     */
    public function setUp()
    {
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
        $this->attachmentFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\AttachmentFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->attachmentMock = $this->getMock(
            '\Mirasvit\MstCore\Model\Attachment',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->attachmentFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->attachmentMock));
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
        $this->storeCollectionFactoryMock = $this->getMock(
            '\Magento\Store\Model\ResourceModel\Store\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->storeCollectionMock = $this->getMock(
            '\Magento\Store\Model\ResourceModel\Store\Collection',
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
        $this->storeCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->storeCollectionMock));
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
        $this->statusCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Status\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->statusCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Status\Collection',
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
        $this->statusCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->statusCollectionMock));
        $this->orderCollectionFactoryMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Order\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->orderCollectionMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Order\Collection',
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
        $this->orderCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->orderCollectionMock));
        $this->resolutionCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Resolution\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resolutionCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Resolution\Collection',
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
        $this->resolutionCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->resolutionCollectionMock));
        $this->configMock = $this->getMock(
            '\Mirasvit\Rma\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->dateMock = $this->getMock(
            '\Magento\Framework\Stdlib\DateTime\DateTime',
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
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->dataHelper = $this->objectManager->getObject(
            '\Mirasvit\Rma\Helper\Data',
            [
                'storeFactory' => $this->storeFactoryMock,
                'orderFactory' => $this->orderFactoryMock,
                'itemFactory' => $this->itemFactoryMock,
                'attachmentFactory' => $this->attachmentFactoryMock,
                'userCollectionFactory' => $this->userCollectionFactoryMock,
                'storeCollectionFactory' => $this->storeCollectionFactoryMock,
                'rmaCollectionFactory' => $this->rmaCollectionFactoryMock,
                'itemCollectionFactory' => $this->itemCollectionFactoryMock,
                'statusCollectionFactory' => $this->statusCollectionFactoryMock,
                'orderCollectionFactory' => $this->orderCollectionFactoryMock,
                'resolutionCollectionFactory' => $this->resolutionCollectionFactoryMock,
                'config' => $this->configMock,
                'date' => $this->dateMock,
                'context' => $this->contextMock,
                'storeManager' => $this->storeManagerMock,
                'localeDate' => $this->localeDateMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->dataHelper, $this->dataHelper);
    }
}
