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



namespace Mirasvit\Rma\Test\Unit\Model\Config\Source\Cms;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Model\Config\Source\Cms\Block
 * @SuppressWarnings(PHPMD)
 */
class BlockTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Config\Source\Cms\Block|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockModel;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\CollectionFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockCollectionFactoryMock;

    /**
     * @var \Magento\Cms\Model\ResourceModel\Block\Collection|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $blockCollectionMock;

    /**
     * @var \Magento\Framework\Model\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->blockCollectionFactoryMock = $this->getMock(
            '\Magento\Cms\Model\ResourceModel\Block\CollectionFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->blockCollectionMock = $this->getMock(
            '\Magento\Cms\Model\ResourceModel\Block\Collection',
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
        $this->blockCollectionFactoryMock->expects($this->any())->method('create')
                ->will($this->returnValue($this->blockCollectionMock));
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->objectManager->getObject(
            '\Magento\Framework\Model\Context',
            [
            ]
        );
        $this->blockModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Config\Source\Cms\Block',
            [
                'blockCollectionFactory' => $this->blockCollectionFactoryMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->blockModel, $this->blockModel);
    }
}
