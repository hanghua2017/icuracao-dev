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



namespace Mirasvit\Rma\Test\Unit\Repository;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;
use \Mockery as m;


/**
 * @covers \Mirasvit\Rma\Repository\RmaRepository
 */
class RmaRepositoryTest extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        m::close();
    }


    /**
     * @test
     * @covers \Mirasvit\Rma\Repository\RmaRepository::get
     */
    public function itShouldGetRma()
    {
        //given
        $rmaId = 2;
        $objectManager = new ObjectManager($this);
        $rma = m::mock('\Mirasvit\Rma\Model\Rma');
        $rma->shouldReceive('getId')->andReturn($rmaId);

        $rmaFactory = m::mock('\Mirasvit\Rma\Model\RmaFactory');
        $rmaFactory->shouldReceive('create')->andReturn($rma);

        //expects
        $rma->shouldReceive('load')->with($rmaId)->andReturn($rma);

        $service = $objectManager->getObject(
            '\Mirasvit\Rma\Repository\RmaRepository',
            [
                'rmaFactory' => $rmaFactory
            ]
        );
        $service->get($rmaId);
    }

}