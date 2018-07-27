<?php
/**
 * @package   Dyode 
 * @author    Sooraj Sathyan
 */
namespace Dyode\ShippingOrder\Controller\Test;

use Dyode\ShippingOrder\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_invoiceModel;
    protected $_shipmentModel;
    /**
     * @var \Dyode\ShippingOrder\Helper\Data
     **/
    protected $_shippingOrderHelper;
    
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dyode\ShippingOrder\Model\Order\Invoice $invoiceModel,
        \Dyode\ShippingOrder\Model\Order\Shipment $shipmentModel,
        \Dyode\ShippingOrder\Helper\Data $shippingOrderHelper
    ) {
        $this->_invoiceModel = $invoiceModel;
        $this->_shipmentModel = $shipmentModel;
        $this->_shippingOrderHelper = $shippingOrderHelper;
        parent::__construct($context);
    }
    public function execute()
    {   
        echo "Hello ";
        // die();
        #dummy values
        // $invoiceNumber = "ZEP58QP";
        // $itemId =  "09A-RA3-RS16FT5050RB";
        // $itemName = "RACE SPORT 16FT RGB ST";
        // $qty = 1;
        // $isSet = false;
        // $respose = $this->_shippingOrderHelper->supplyWebItem($invoiceNumber, $itemId, $itemName, $qty, $isSet);
        // print_r($respose->INFO);
        // die();
        $orderId = 5;   //order id for which want to create invoice
        // $invoiceResponse = $this->_invoiceModel->createInvoice($orderId);
        $shipmentResponse = $this->_shipmentModel->createShipment($orderId);
        die();
        
    }
}