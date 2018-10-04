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



namespace Mirasvit\Rma\Test\Unit\Controller\Adminhtml\Rma;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Controller\Adminhtml\Rma\Exchange
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.TooManyFields)
 */
class ExchangeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Controller\Adminhtml\Rma\Exchange|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaController;

    /**
     * @var \Mirasvit\Rma\Model\RmaFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaFactoryMock;

    /**
     * @var \Mirasvit\Rma\Model\Rma|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\TicketFactory|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketFactoryMock;

    /**
     * @var \Mirasvit\Helpdesk\Model\Ticket|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $ticketMock;

    /**
     * @var \Mirasvit\Rma\Helper\Process|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaProcessMock;

    /**
     * @var \Mirasvit\Rma\Helper\Order|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $rmaOrderMock;

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
     *
     */
    public function setUp()
    {
        $this->rmaFactoryMock = $this->getMock('\Mirasvit\Rma\Model\RmaFactory', ['create'], [], '', false);
        $this->rmaMock = $this->getMock('\Mirasvit\Rma\Model\Rma', ['load', 'save', 'delete'], [], '', false);
        $this->rmaFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($this->rmaMock));
        $this->ticketFactoryMock = $this->getMock('\Mirasvit\Helpdesk\Model\TicketFactory', ['create'], [], '', false);
        $this->ticketMock = $this->getMock(
            '\Mirasvit\Helpdesk\Model\Ticket',
            ['load', 'save', 'delete'],
            [],
            '',
            false
        );
        $this->ticketFactoryMock->expects($this->any())->method('create')
            ->will($this->returnValue($this->ticketMock));
        $this->rmaProcessMock = $this->getMock('\Mirasvit\Rma\Helper\Process', [], [], '', false);
        $this->rmaOrderMock = $this->getMock('\Mirasvit\Rma\Helper\Order', [], [], '', false);
        $this->registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $this->backendSessionMock = $this->getMock('\Magento\Backend\Model\Session', [], [], '', false);
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
        $this->rmaController = $this->objectManager->getObject(
            '\Mirasvit\Rma\Controller\Adminhtml\Rma\Exchange',
            [
                'rmaFactory'    => $this->rmaFactoryMock,
                'ticketFactory' => $this->ticketFactoryMock,
                'rmaProcess'    => $this->rmaProcessMock,
                'rmaOrder'      => $this->rmaOrderMock,
                'registry'      => $this->registryMock,
                'context'       => $this->contextMock,
            ]
        );
    }

    /**
     *
     */
    public function testDummy()
    {
        $this->assertEquals($this->rmaController, $this->rmaController);
    }
}
