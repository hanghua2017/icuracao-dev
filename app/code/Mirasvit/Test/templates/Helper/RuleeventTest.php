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
 * @covers \Mirasvit\Rma\Helper\Ruleevent
 * @SuppressWarnings(PHPMD)
 */
class RuleeventTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Helper\Ruleevent|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleeventHelper;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Rule\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleCollectionFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\ResourceModel\Rule\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ruleCollectionMock;

    /**
     * @var \Mirasvit\Rma\Model\Config|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $configMock;

    /**
     * @var \Mirasvit\Rma\Helper\Tag|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaTagMock;

    /**
     * @var \Mirasvit\Rma\Helper\Mail|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaMailMock;

    /**
     * @var \Magento\Framework\App\Helper\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->ruleCollectionFactoryMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Rule\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->ruleCollectionMock = $this->getMock(
            '\Mirasvit\Rma\Model\ResourceModel\Rule\Collection',
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
        $this->ruleCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->ruleCollectionMock));
        $this->configMock = $this->getMock(
            '\Mirasvit\Rma\Model\Config',
            [],
            [],
            '',
            false
        );
        $this->rmaTagMock = $this->getMock(
            '\Mirasvit\Rma\Helper\Tag',
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
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\App\Helper\Context',
            [
            ]
        );
        $this->ruleeventHelper = $this->objectManager->getObject(
            '\Mirasvit\Rma\Helper\Ruleevent',
            [
                'ruleCollectionFactory' => $this->ruleCollectionFactoryMock,
                'config' => $this->configMock,
                'rmaTag' => $this->rmaTagMock,
                'rmaMail' => $this->rmaMailMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->ruleeventHelper, $this->ruleeventHelper);
    }
}
