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



namespace Mirasvit\Rma\Test\Unit\Model\Config\Source\Is\Manual;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager as ObjectManager;

/**
 * @covers \Mirasvit\Rma\Model\Config\Source\Is\Manual\Creditmemo
 * @SuppressWarnings(PHPMD)
 */
class CreditmemoTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var \Mirasvit\Rma\Model\Config\Source\Is\Manual\Creditmemo|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $creditmemoModel;

    /**
     * setup tests.
     */
    public function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->creditmemoModel = $this->objectManager->getObject(
            '\Mirasvit\Rma\Model\Config\Source\Is\Manual\Creditmemo',
            [
            ]
        );
    }

    /**
     * dummy test.
     */
    public function testDummy()
    {
        $this->assertEquals($this->creditmemoModel, $this->creditmemoModel);
    }
}
