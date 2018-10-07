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
 * @covers \Mirasvit\Rma\Model\Item
 * @SuppressWarnings(PHPMD)
 */
class ItemTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemModel;

    /**
     * @var \Magento\Catalog\Model\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productFactoryMock;

    /**
     * @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $productMock;

    /**
     * @var \Mirasvit\Rma\Model\ReasonFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reasonFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Reason|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $reasonMock;

    /**
     * @var \Mirasvit\Rma\Model\ResolutionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolutionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Resolution|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resolutionMock;

    /**
     * @var \Mirasvit\Rma\Model\ConditionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $conditionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Condition|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $conditionMock;

    /**
     * @var \Mirasvit\Rma\Model\RmaFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaMock;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemFactoryMock;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $stockItemMock;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderItemFactoryMock;

    /**
     * @var \Magento\Sales\Model\Order\Item|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $orderItemMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Item\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Item\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $itemCollectionMock;

    /**
     * @var \Mirasvit\Rma\Helper\Locale|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaLocaleMock;

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
        $this->productFactoryMock = $this->getMock(
            '\Magento\Catalog\Model\ProductFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->productMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->productFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->productMock));
        $this->reasonFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ReasonFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->reasonMock = $this->getMock(
            '\Mirasvit\Rma\Model\Reason',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->reasonFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->reasonMock));
        $this->resolutionFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResolutionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resolutionMock = $this->getMock(
            '\Mirasvit\Rma\Model\Resolution',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->resolutionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->resolutionMock));
        $this->conditionFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ConditionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->conditionMock = $this->getMock(
            '\Mirasvit\Rma\Model\Condition',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->conditionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->conditionMock));
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
        $this->stockItemFactoryMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\ItemFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->stockItemMock = $this->getMock(
            '\Magento\CatalogInventory\Model\Stock\Item',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->stockItemFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->stockItemMock));
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
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Item',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Item\Collection',
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
        $this->itemModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Item',
            [
                'productFactory' => $this->productFactoryMock,
                'reasonFactory' => $this->reasonFactoryMock,
                'resolutionFactory' => $this->resolutionFactoryMock,
                'conditionFactory' => $this->conditionFactoryMock,
                'rmaFactory' => $this->rmaFactoryMock,
                'stockItemFactory' => $this->stockItemFactoryMock,
                'orderItemFactory' => $this->orderItemFactoryMock,
                'itemCollectionFactory' => $this->itemCollectionFactoryMock,
                'rmaLocale' => $this->rmaLocaleMock,
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
        $this->assertEquals($this->itemModel, $this->itemModel);
    }
}
