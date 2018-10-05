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
 * @covers \Mirasvit\Rma\Model\Rule\Condition\Combine
 * @SuppressWarnings(PHPMD)
 */
class CombineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Condition\Combine|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $combineModel;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Condition\ProductFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionProductFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Condition\Product|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionProductMock;

    /**
     * @var \Magento\Salesrule\Model\Rule\Condition\AddressFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionAddressFactoryMock;

    /**
     * @var \Magento\Salesrule\Model\Rule\Condition\Address|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionAddressMock;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Condition\CustomFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionCustomFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Condition\Custom|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionCustomMock;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Condition\RmaFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionRmaFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Rule\Condition\Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleConditionRmaMock;

    /**
     * @var \Mirasvit\Rma\Helper\Rule|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaRuleMock;

    /**
     * @var \Magento\Rule\Model\Condition\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Framework\App\ResourceConnection|\PHPUnit_Framework_MockObject_MockObject
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
        $this->ruleConditionProductFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rule\Condition\ProductFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleConditionProductMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rule\Condition\Product',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->ruleConditionProductFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleConditionProductMock));
        $this->ruleConditionAddressFactoryMock = $this->getMock(
            '\Magento\Salesrule\Model\Rule\Condition\AddressFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleConditionAddressMock = $this->getMock(
            '\Magento\Salesrule\Model\Rule\Condition\Address',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->ruleConditionAddressFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleConditionAddressMock));
        $this->ruleConditionCustomFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rule\Condition\CustomFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleConditionCustomMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rule\Condition\Custom',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->ruleConditionCustomFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleConditionCustomMock));
        $this->ruleConditionRmaFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rule\Condition\RmaFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleConditionRmaMock = $this->getMock(
            '\Mirasvit\Rma\Model\Rule\Condition\Rma',
            ['load',
            'save',
            'delete', ],
            [],
            '',
            false
        );
        $this->ruleConditionRmaFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleConditionRmaMock));
        $this->rmaRuleMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Rule',
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
            '\Mirasvit\Rma\Model\ResourceModel\Rule\Condition\Combine',
            [],
            [],
            '',
            false
        );
        $this->resourceCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Rule\Condition\Combine\Collection',
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
        $this->combineModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Rule\Condition\Combine',
            [
                'ruleConditionProductFactory' => $this->ruleConditionProductFactoryMock,
                'ruleConditionAddressFactory' => $this->ruleConditionAddressFactoryMock,
                'ruleConditionCustomFactory' => $this->ruleConditionCustomFactoryMock,
                'ruleConditionRmaFactory' => $this->ruleConditionRmaFactoryMock,
                'rmaRule' => $this->rmaRuleMock,
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
        $this->assertEquals($this->combineModel, $this->combineModel);
    }
}
