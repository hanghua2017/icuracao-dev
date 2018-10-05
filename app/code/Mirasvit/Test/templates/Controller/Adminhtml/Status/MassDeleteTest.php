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



namespace Mirasvit\Rma\Test\Unit\Controller\Adminhtml\Status;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Controller\Adminhtml\Status\MassDelete
 * @SuppressWarnings(PHPMD)
 */
class MassDeleteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Controller\Adminhtml\Status\MassDelete|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusController;

    /**
     * @var \Mirasvit\Rma\Model\StatusFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Status|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $statusMock;

    /**
     * @var \Mirasvit\MstCore\Helper\Date|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $mstcoreDateMock;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $localeDateMock;

    /**
     * @var \Magento\Framework\Registry|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $registryMock;

    /**
     * @var \Magento\Backend\App\Action\Context|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $contextMock;

    /**
     * @var \Magento\Backend\Model\Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $backendSessionMock;

    /**
     * @var \Magento\Framework\View\Result\PageFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultFactoryMock;

    /**
     * @var \Magento\Backend\Model\View\Result\Page|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $resultPageMock;

    /**
     * @var \Magento\Framework\App\Response\RedirectInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $redirectMock;

    /**
     * @var \Magento\Framework\App\RequestInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $requestMock;

    /**
     * @var \Magento\Framework\Message\ManagerInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $messageManagerMock;

    /**
     * setup tests.
     */
    public function setUp()
    {
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
        $this->mstcoreDateMock = $this->getMock(
            '\Mirasvit\MstCore\Helper\Date',
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
        $this->backendSessionMock = $this->getMock(
            '\Magento\Backend\Model\Session',
            [],
            [],
            '',
            false
        );
        $this->requestMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\RequestInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->resultFactoryMock = $this->getMock(
            'Magento\Framework\Controller\ResultFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resultPageMock = $this->getMock('Magento\Backend\Model\View\Result\Page', [], [], '', false);
        $this->resultFactoryMock->expects($this->any())
           ->method('create')
           ->willReturn($this->resultPageMock);

        $this->redirectMock = $this->getMockForAbstractClass(
            'Magento\Framework\App\Response\RedirectInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->messageManagerMock = $this->getMockForAbstractClass(
            'Magento\Framework\Message\ManagerInterface',
            [],
            '',
            false,
            true,
            true,
            []
        );
        $this->objectManager = new ObjectManager($this);
        $this->contextMock = $this->getMock('\Magento\Backend\App\Action\Context', [], [], '', false);
        $this->contextMock->expects($this->any())->method('getRequest')->willReturn($this->requestMock);
        $this->contextMock->expects($this->any())->method('getObjectManager')->willReturn($this->objectManager);
        $this->contextMock->expects($this->any())->method('getResultFactory')->willReturn($this->resultFactoryMock);
        $this->contextMock->expects($this->any())->method('getRedirect')->willReturn($this->redirectMock);
        $this->contextMock->expects($this->any())->method('getMessageManager')->willReturn($this->messageManagerMock);
        $this->statusController = $this->objectManager->getObject(
            '\Mirasvit\Rma\Controller\Adminhtml\Status\MassDelete',
            [
                'statusFactory' => $this->statusFactoryMock,
                'mstcoreDate' => $this->mstcoreDateMock,
                'localeDate' => $this->localeDateMock,
                'registry' => $this->registryMock,
                'context' => $this->contextMock,
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->statusController, $this->statusController);
    }
}
