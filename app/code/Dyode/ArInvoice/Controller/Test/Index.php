<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
 */
namespace Dyode\ArInvoice\Controller\Test;

use Dyode\ShippingOrder\Helper\Data;

class Index extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Dyode\ArInvoice\Model\ArInvoice $arInvoice
     **/
    protected $arInvoice;

    /**
     * @var \Dyode\ArInvoice\Helper\Data $_arInvoiceHelper 
     **/
    protected $_arInvoiceHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    protected $_orderCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Dyode\ArInvoice\Model\ArInvoice $arInvoice,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
    ) {
        $this->arInvoice = $arInvoice;
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_arInvoiceHelper = $arInvoiceHelper;
        parent::__construct($context);
    }

    public function execute()
    {
        // $invoiceNo = "ZEP5903";
        // $customerId = "53208833";
        // $firstName = "TED";
        // $lastName = "JOHN";
        // $email = "someone@somesite.tld";
        // $phone = "(999)999-9999";
        // $inputArray = array(
        //     "invoice" => $invoiceNo,
        //     "cust_id" => $customerId,
        //     "f_name" => $firstName,
        //     "l_name" => $lastName,
        //     "email" => $email,
        //     "cell_no" => $phone,
        //     "items" => array(
        //         array(
        //             "23H-N05-MC544LL/A",
        //             "23Y-417-S5094LL/A"
        //         )
        //     )
        // );
        // echo "<pre>";
        // print_r($inputArray);
        // // die();
        // echo "<br>";
        // $result = $this->_arInvoiceHelper->appleCareSetWarranty($inputArray);
        // print_r($result);

        // $warrantyList = $this->_arInvoiceHelper->appleCareListWarranties();
        // print_r($warrantyList);
        // echo "</pre>";
        // die();
        // $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        // $collection->addFieldToFilter('status', 'approved_fraud');
        // $collection->addFieldToFilter('status', 'credit_review');
        
        // // print_r($orderCollection);
        // // die();
        // echo "Hello";
        // // die();
        // foreach ($collection as $salesOrder) {
        //     # code...
        //     // print_r($salesOrder);
        //     echo $salesOrder->getId();
            
        //     print_r($salesOrder->getData());
        //     echo "<br>";
        //     // die();
        // }
        
        // $orders = $this->arInvoice->getSalesOrderCollection();
        // $this->arInvoice->createInvoice(3);
        // die();
        // $itemId = '32A-061-101946';
        // $_allLocationsZipcodes = array('01' => 90015, '09' => 91402, '16' => 90280, '22' => 90255, '29' => 92408, '33' => 90280, '35' => 92708, '38' => 91710, '51' => 92801, '40' => 85033, '57' => 85713, '64' => 89107);
        // $locations = implode(array_keys($_allLocationsZipcodes),',');
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

        // echo $inventoryLevel = $this->_arInvoiceHelper->getDomesticInventoryLocation($itemId, 41, 35801, 40);
        // die();
        // echo "Hello";
        $this->arInvoice->prepareOrderItems(13045);
        // $this->arInvoice->createInvoice(13069);
        // var_dump($orders);
        die();
    }
}