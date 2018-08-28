<?php
/**
 * @package   Dyode 
 * @author    Sooraj Sathyan
 */
namespace Dyode\ArInvoice\Controller\Test;

use Dyode\ShippingOrder\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Dyode\ArInvoice\Model\OrderCollection $orderCollection
     **/
    protected $orderCollection;

    /**
     * @var \Dyode\ArInvoice\Helper\Data $_arInvoiceHelper 
     **/
    protected $_arInvoiceHelper;
    

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dyode\ArInvoice\Model\OrderCollection $orderCollection,
        \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
    ) {
        $this->orderCollection = $orderCollection;
        $this->_arInvoiceHelper = $arInvoiceHelper;
        parent::__construct($context);
    }
    public function execute()
    {   
        // $orders = $this->orderCollection->getSalesOrderCollection();
        // $this->orderCollection->createInvoice(3);
        $itemId = '32A-061-101946';
        $_allLocationsZipcodes = array('01' => 90015, '09' => 91402, '16' => 90280, '22' => 90255, '29' => 92408, '33' => 90280, '35' => 92708, '38' => 91710, '51' => 92801, '40' => 85033, '57' => 85713, '64' => 89107);
        $locations = implode(array_keys($_allLocationsZipcodes),',');
        // $inventoryLevel = $this->_arInvoiceHelper->inventoryLevel($itemId, $locations);
        // if ($inventoryLevel->OK) {
        //     # code...
        //     foreach ($inventoryLevel->LIST as $value) {
        //         # code...
        //         print_r($value->location);
        //         echo " : ";
        //         print_r($value->quantity);
        //         echo " || ";
        //     }
        // } else {
        //     # code...
        //     throw new Exception("Inventory Level : " . $inventoryLevel->INFO);
        // }
        // $inventoryLevel = $this->_arInvoiceHelper->getDomesticInventory($itemId, 3);
        // die();
        $this->orderCollection->prepareOrderItems(1);
        echo "Hello";
        // var_dump($orders);
        die();
    }
}