<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */
namespace Dyode\ArInvoice\Model;

// use Magento\Framework\View\Element\Template\Context;
use Dyode\ArInvoice\Helper\Data;
// use Magento\Framework\Model\Context;
// use Magento\Framework\Registry;

class OrderCollection extends \Magento\Framework\Model\AbstractModel// implements \Magento\Framework\DataObject\IdentityInterface
{   
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     **/
    protected $orderCollectionFactory;

    protected $_pageFactory;
    
    protected $data;
    
    protected $_helper;

	public function __construct(
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Dyode\ArInvoice\Helper\Data $helper,
        \Magento\Framework\Registry $data
    ) {
		$this->helper = $helper;
        $this->_pageFactory = $pageFactory;
        $this->_helper = $helper;
		return parent::__construct($context, $data);
	}

    public function getSalesOrderCollection()
    {       
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderCollection = $objectManager->create('\Magento\Sales\Model\ResourceModel\Order\Collection');
        $orderCollection->load();
        // print_r($orderCollection->getData());
        foreach ($orderCollection as $order) {
            $paymentMethod = $order->getPayment()->getMethod();
            // // $methodTitle = $method->getTitle();
            // print_r($order->getData());
            // print_r($order->getPayment()->getMethod());
            // print_r($methodTitle);
            // echo " ";

            // Validating the Payment Method
            // if (strpos($paymentMethod, 'authorizenet') !== false) {
            //     echo 'Authorize.net';
            // } else {
            //     echo 'false';
            // }
            // echo " ";

            // Validating the Account Number
            // if (!empty($order->getCustomerId())) {
            //     echo $this->_helper->validateAccountNumber($order->getCustomerId());
            //     // var_dump($order->getCustomerId());    
            // } else {
            //     echo "Account Number not found!";
            // }

            echo $order->getState();
            echo $order->getStatus();
            echo " ";
        }
        $customerId = "500-8555";
        $string = "hello";
        $int = 123;
        $float = 405.20;
        $boolean = true;
        $inputArray = array(
            'CustomerID' => '658138',
            'CreateDate' => '2018-06-08',
            'CreateTime' => '2:31:23',
            'WebReference' => $string,
            'SubTotal' => '$199.99',
            'TaxAmount' => '$18.50',
            'DestinationZip' => '93906',
            'ShipCharge' => '$15.28',
            'ShipDescription' => 'United Parcel Service',
            'DownPmt' => '0',
            'EmpID' => $string,
            'DiscountAmount' => '$15.00',
            'DiscountDescription' => $string,
            'ShippingDiscount' => '$0',
            'Detail' => array(
                'TEstLine' => array(
                    'ItemType' => $string,
                    'Item_ID' => $string,
                    'Item_Name' => $string,
                    'Model' => 'Apple',
                    'ItemSet' => 'Watches',
                    'Qty' => 1,
                    'Price' => '199.99',
                    'Cost' => '199.99',
                    'Taxable' => $string,
                    'WebVendor' => 2,
                    'From' => $string,
                    'PickUp' => 'false',
                    'OrdItemID' => $int,
                    'Tax_Amt' => '$18.50',
                    'Tax_Rate' => '9.25'  
                )
            )
        );
        $inputArray1 = array(
            'CustomerID' => '658138',
            'CreateDate' => '2018-06-08',
            'CreateTime' => '2:31:23',
            'WebReference' => $string,
            'SubTotal' => '$199.99',
            'TaxAmount' => '$18.50',
            'DestinationZip' => '93906',
            'ShipCharge' => '$15.28',
            'ShipDescription' => 'United Parcel Service',
            'DownPmt' => '0',
            'EmpID' => $string,
            'SubAcct' => $string,
            'NoOfPmts' => $string,
            'DueDate' => $string,
            'RegAcctInfo' => $string,
            'DiscountAmount' => '$15.00',
            'DiscountDescription' => $string,
            'ShippingDiscount' => '$0',
            'Detail' => array(
                'TEstLine' => array(
                    'ItemType' => $string,
                    'Item_ID' => $string,
                    'Item_Name' => $string,
                    'Model' => 'Apple',
                    'ItemSet' => 'Watches',
                    'Qty' => 1,
                    'Price' => '199.99',
                    'Cost' => '199.99',
                    'Taxable' => $string,
                    'WebVendor' => 2,
                    'From' => $string,
                    'PickUp' => 'false',
                    'OrdItemID' => $int,
                    'Tax_Amt' => '$18.50',
                    'Tax_Rate' => '9.25'
                )
            )
        );
        
        $hasPaymentPlan = false;
        
        if (!$hasPaymentPlan) {
            // Creating Invoice using API CreateInvoiceRev
            $createInvoiceResponse = $this->_helper->createInvoiceRev($inputArray);
        }
        else {
            // Creating Invoice using API CreateInvoiceReg
            $createInvoiceResponse = $this->_helper->createInvoiceReg($inputArray1);
        }

        // Validating the Payment Method
        if (strpos($createInvoiceResponse, 'ERROR') !== false) {
            echo 'ERROR';
        } else {
            echo 'NOT ERROR';
        }
        echo " ";

        die();
        // $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        // // $this->orderCollectionFactory->addFieldToFilter('status','complete');
        // return $collection;
    }
    
}
