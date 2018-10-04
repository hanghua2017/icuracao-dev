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
 * @covers \Mirasvit\Rma\Model\Rule
 * @SuppressWarnings(PHPMD)
 */
class RuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleModel;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Condition\CombineFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionCombineFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionCombineMock;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Action\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleActionCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Action\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleActionCollectionMock;

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
        $this->ruleConditionCombineFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rule\Condition\CombineFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleConditionCombineMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rule\Condition\Combine',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->ruleConditionCombineFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleConditionCombineMock));
        $this->ruleActionCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rule\Action\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleActionCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rule\Action\Collection',
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
        $this->ruleActionCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleActionCollectionMock));
        $this->registryMock = $this->getMock(
            '\Magento\Framework\Registry',
            [],
            [],
            '',
            false
        );
        $this->resourceMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Rule',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Rule\Collection',
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
        $this->ruleModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Rule',
            [
                'ruleConditionCombineFactory' => $this->ruleConditionCombineFactoryMock,
                'ruleActionCollectionFactory' => $this->ruleActionCollectionFactoryMock,
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
        $this->assertEquals($this->ruleModel, $this->ruleModel);
    }
}
