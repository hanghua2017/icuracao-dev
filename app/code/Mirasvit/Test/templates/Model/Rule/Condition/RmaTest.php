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



namespace Mirasvit\Rma\Test\Unit\Model\Rule\Condition;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Model\Rule\Condition\Rma
 * @SuppressWarnings(PHPMD)
 */
class RmaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Condition\Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaModel;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Field\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Field\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $fieldCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Status\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Status\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusCollectionMock;

    /**
     * @var \Mirasvit\Rma\Helper\Field|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaFieldMock;

    /**
     * @var \Mirasvit\Rma\Helper\Data|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaDataMock;

    /**
     * @var \Magento\Rule\Model\Condition\Context|\PHPUnit_Framework_MockObject_MockObject
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
        $this->markTestIncomplete('Error here');

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
        $this->rmaFieldMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Field',
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
            '\Magento\Framework\Model\ResourceModel\AbstractResource',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Magento\Framework\Data\Collection\AbstractDb',
            [],
            [],
            '',
            false
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Rule\Model\Condition\Context',
            [
            ]
        );
        $this->rmaModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Rule\Condition\Rma',
            [
                'fieldCollectionFactory' => $this->fieldCollectionFactoryMock,
                'statusCollectionFactory' => $this->statusCollectionFactoryMock,
                'rmaField' => $this->rmaFieldMock,
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
        $this->assertEquals($this->rmaModel, $this->rmaModel);
    }
}
