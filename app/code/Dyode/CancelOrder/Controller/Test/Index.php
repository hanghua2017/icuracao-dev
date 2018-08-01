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
        $invoiceNumber = "ZEP58P6";
        $itemId = "09A-RA3-RS16FT5050RB";
        $qty = 2;
        $this->_cancelOrderHelper->adjustItem($invoiceNumber, $itemId, $qty);//, $newSubTotal, $newTotalTax, $newPrice, $newDescription);

        // $this->_cancelOrderHelper->cancelEstimate($invoiceNumber);
    }
}