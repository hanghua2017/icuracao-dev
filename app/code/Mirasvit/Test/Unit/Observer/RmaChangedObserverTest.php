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



namespace Mirasvit\Rma\Test\Unit\Observer;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use \Mockery as m;


/**
 * @covers \Mirasvit\Rma\Observer\RmaChangedObserver
 */
class RmaChangedObserverTest extends \PHPUnit_Framework_TestCase
{
    use \Codeception\Specify;


    /**
     * @var \Mockery\MockInterface
     */
    protected $status;

    /**
     * @var \Mockery\MockInterface
     */
    protected $rmaMail;

    /**
     * @var \Mockery\MockInterface
     */
    protected $rma;


    /**
     * @var \Mirasvit\Rma\Observer\RmaChangedObserver
     */
    protected $service;

    public function tearDown()
    {
        m::close();
    }

    public function setUp()
    {
        $this->rmaMail = m::mock('\Mirasvit\Rma\Helper\Mail');
        $this->rma = m::mock('\Mirasvit\Rma\Model\Rma');
        $this->status = m::mock('\Mirasvit\Rma\Model\Status');
        $this->rma->shouldReceive('getStatus')->andReturn($this->status);
        $this->objectManager = new ObjectManager($this);

        $this->service = $this->objectManager->getObject(
            '\Mirasvit\Rma\Observer\RmaChangedObserver',
            [
                'rmaMail' => $this->rmaMail
            ]
        );
    }

    /**
     * @test
     */
    public function itShouldNotSendEmailsWhenStatusOrUserAreNotChanged()
    {
        $this->rma->shouldReceive('getStatusId')->andReturn(1);
        $this->rma->shouldReceive('getOrigData')->with('status_id')->andReturn(1);

        $this->rma->shouldReceive('getUserId')->andReturn(1);
        $this->rma->shouldReceive('getOrigData')->with('user_id')->andReturn(1);
        $this->status->shouldReceive('getAdminMessage')->andReturn('some message');
        $this->status->shouldIgnoreMissing();
        $this->rmaMail->shouldIgnoreMissing();

        $this->status->shouldReceive('getCustomerMessage')->never();
        $this->service->notifyRmaChange($this->rma);
    }

    /**
     * @test
     */
    public function itShouldSendAllKindsOfEmails()
    {
        //GIVEN
        $message = 'some message';
        $this->rma->shouldReceive('getUser')->andReturn(false);
        $this->rma->shouldReceive('getStatusId')->andReturn(1);
        $this->rma->shouldReceive('getOrigData')->andReturn(2);
        $this->status->shouldReceive('getCustomerMessage')->andReturn($message);
        $this->status->shouldReceive('getAdminMessage')->andReturn($message);
        $this->status->shouldReceive('getHistoryMessage')->andReturn($message);
        $this->rmaMail->shouldReceive('parseVariables')->andReturn($message)->times(1);

        //EXPECTATIONS
        //mail should send emails
        $this->rmaMail->shouldReceive('sendNotificationCustomerEmail')
            ->with($this->rma, $message, true)->andReturn($message)->times(1);
        $this->rmaMail->shouldReceive('sendNotificationAdminEmail')->andReturn($message)->times(1);

        //RUN
        $this->service->notifyRmaChange($this->rma);
    }
}
