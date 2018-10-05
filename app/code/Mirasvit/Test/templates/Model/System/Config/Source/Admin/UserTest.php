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



namespace Mirasvit\Rma\Test\Unit\Model\System\Config\Source\Admin;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Model\System\Config\Source\Admin\User
 * @SuppressWarnings(PHPMD)
 */
class UserTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\System\Config\Source\Admin\User|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userModel;

    /**
     * @var \Magento\User\Model\ResourceModel\User\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userCollectionFactoryMock;

    /**
     * @var \Magento\User\Model\ResourceModel\User\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userCollectionMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
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
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->userModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\System\Config\Source\Admin\User',
            [
                'userCollectionFactory' => $this->userCollectionFactoryMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->userModel, $this->userModel);
    }
}
