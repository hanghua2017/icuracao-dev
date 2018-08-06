<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */
namespace Dyode\CancelOrder\Controller\Test;

use Dyode\CancelOrder\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_cancelOrderHelper;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dyode\CancelOrder\Helper\Data $cancelOrderHelper
    ) {
        $this->_cancelOrderHelper = $cancelOrderHelper;
        parent::__construct($context);
    }
    
    public function execute()
    {
        $invoiceNumber = "ZEP58QX";
        $itemId = "32O-285-42LB5600";
        $qty = 1;
        // $this->_cancelOrderHelper->adjustItem($invoiceNumber, $itemId, $qty);//, $newSubTotal, $newTotalTax, $newPrice, $newDescription);
        $this->_cancelOrderHelper->adjustItem($invoiceNumber, $itemId, $qty, 30.0, 2.0, 32.0);
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		// $logger = $objectManager->get("Psr\Log\LoggerInterface");
        // $response = $this->_cancelOrderHelper->cancelEstimate($invoiceNumber);
        // print_r($response->INFO);
        // $logger->info('Response: ' . $response->INFO);
    }
}