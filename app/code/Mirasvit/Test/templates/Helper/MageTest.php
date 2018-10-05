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

/**
 * @covers \Mirasvit\Rma\Helper\Mage
 * @SuppressWarnings(PHPMD)
 */
class MageTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Helper\Mage|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mageHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Grid\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderGridCollectionMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionFactoryMock;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderCollectionMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Backend\Model\Url|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendUrlManagerMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->orderGridCollectionMock = $this->getMock(
            '\Magento\Sales\Model\ResourceModel\Order\Grid\Collection',
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
        $this->backendUrlManagerMock = $this->getMock(
            '\Magento\Backend\Model\Url',
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
        $this->mageHelper = $this->objectManager->getObject(
            '\Mirasvit\Rma\Helper\Mage',
            [
                'orderCollectionFactory' => $this->orderCollectionFactoryMock,
                'context'                => $this->contextMock,
                'backendUrlManager'      => $this->backendUrlManagerMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->mageHelper, $this->mageHelper);
    }
}
