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



/**
 * @covers \Mirasvit\Rma\Observer\RmaChangedObserver
 * @codingStandardsIgnoreFile
 */
class RmaChangedObserverTest extends PHPUsableTest
{
    public function tests() {
        PHPUsableTest::$current_test = $this;
        // Given
        describe('#onRmaStatusChange', function($test) {
            before(function($test) {
                $this->rmaMailMock = $this->getMock(
                    '\Mirasvit\Rma\Helper\Mail',
                    ['sendNotificationCustomerEmail'],
                    [],
                    '',
                    false
                );

                $this->rmaMock = $this->getMock(
                    '\Mirasvit\Rma\Model\Rma',
                    ['getStatus', 'save', 'getId'],
                    [],
                    '',
                    false
                );

                $this->statusMock = $this->getMock(
                    '\Mirasvit\Rma\Model\Status',
                    [
                        'load',
                        'save',
                        'delete',
                        'getCustomerMessage',
                        'getAdminMessage',
                        'getHistoryMessage',
                    ],
                    [],
                    '',
                    false
                );
                $this->rmaMock->expects($this->any())->method('getStatus')
                    ->will($this->returnValue($this->statusMock));

                $this->objectManager = new ObjectManager($this);

                $this->service = $this->objectManager->getObject(
                    '\Mirasvit\Rma\Observer\RmaChangedObserver',
                    [
                        'rmaMail' => $this->rmaMailMock
                    ]
                );
            });

            describe('with customer message', function($test) {
                before(function($test) {
                    $this->message = 'some message';
                    $this->statusMock->expects($this->any())->method('getCustomerMessage')
                        ->will($this->returnValue($this->message));
                });

                it ('should send message to customer', function($test) {
                    $this->rmaMailMock->expects($this->once())->method('sendNotificationCustomerEmail')
                        ->with($this->rmaMock, $this->message, true)
                        ->will($this->returnValue($this->message));
                    $this->service->onRmaStatusChange($this->rmaMock);
                    $this->assertTrue(true);
                });
            });

            describe('without customer message', function($test) {
                before(function($test) {
                    $this->statusMock->expects($this->any())->method('getCustomerMessage')
                        ->will($this->returnValue(false));
                });

                it ('should not send message to customer', function($test) {
                    $this->rmaMailMock->expects($this->once())->method('sendNotificationCustomerEmail')
                        ->with($this->rmaMock, $this->message, true)
                        ->will($this->returnValue($this->message));
                    $this->service->onRmaStatusChange($this->rmaMock);
                    $this->assertTrue(true);
                });
            });
        });
    }
}
