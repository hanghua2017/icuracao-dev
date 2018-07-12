<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */
namespace Dyode\ArInvoice\Model;

// use Magento\Framework\View\Element\Template\Context;
use Dyode\ArInvoice\Helper\Data;
use Magento\Sales\Model\Order;
// use Magento\Framework\Model\Context;
// use Magento\Framework\Registry;

class OrderCollection extends \Magento\Framework\Model\AbstractModel// implements \Magento\Framework\DataObject\IdentityInterface
{   
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     **/
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Framework\View\Result\PageFactory $pageFactory
     **/
    protected $_pageFactory;
    
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
     */
    protected $_statusCollectionFactory;
    /**
     * @var \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
     **/
    protected $_arInvoiceHelper;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory,
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
     * @param \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
     * @param \Magento\Framework\Registry $data
     */
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory,
        \Dyode\ArInvoice\Helper\Data $arInvoiceHelper,
        \Magento\Framework\Registry $data
    ) {
		$this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_pageFactory = $pageFactory;
        $this->_statusCollectionFactory = $statusCollectionFactory;
        $this->_arInvoiceHelper = $arInvoiceHelper;
		return parent::__construct($context, $data);
	}

    /**
     * Get Sales Order Collection for AR Invoice
     */
    public function createInvoice($orderId)
    {
        
    }

    /**
     * Get Sales Order Collection for AR Invoice
     */
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
            //     echo $this->_arInvoiceHelper->validateAccountNumber($order->getCustomerId());
            //     // var_dump($order->getCustomerId());    
            // } else {
            //     echo "Account Number not found!";
            // }
        //     $inputArray = array();
        //     if (!empty($order)) {
        //          $inputArray = array(
        //             'CustomerID' => $order->getCustomerId(), 
        //             'CreateDate' => '2018-06-08',
        //             'CreateTime' => '2:31:23',
        //             'WebReference' => $string,
        //             'SubTotal' => '$199.99',
        //             'TaxAmount' => '$18.50',
        //             'DestinationZip' => '93906',
        //             'ShipCharge' => '$15.28',
        //             'ShipDescription' => 'United Parcel Service',
        //             'DownPmt' => '0',
        //             'EmpID' => $string,
        //             'DiscountAmount' => '$15.00',
        //             'DiscountDescription' => $string,
        //             'ShippingDiscount' => '$0',
        //         );
        //     }
            echo $order->getCustomerId();
            // $order->setState("new")->setStatus("processing");
            
            $order->setState("processing")->setStatus("estimate_issue");    // Change the Order Status and Order State
            $order->addStatusToHistory($order->getStatus(), 'Estimate not Issued');     // Add Comment to Order History
            $order->save();     // Save the Changes in Order Status & History    
            
            // echo $order->getIncrementId();
            // // echo $order->getState();
            // // echo $order->getStatus();  
            // $histories = $order->getStatusHistories();
            // $latestHistoryComment = array_pop($histories);
            // echo $comment = $latestHistoryComment->getComment();
            // echo " ";
            // echo $status = $latestHistoryComment->getStatus();
            $shippingStreet = '3325 W PICO BLVD APT 9';
            $shippingZip = '90019';
            $addressMismatch = $this->_arInvoiceHelper->validateAddress($order->getCustomerId(), $shippingStreet, $shippingZip);
            $customerStatus = $this->_arInvoiceHelper->isCustomerActive($order->getCustomerId());
            // echo " ";
            if ($customerStatus == "Soft" || $customerStatus == "NO" || addressMismatch == True) {
                // echo "Hello World";
                $order->setState("payment_review")->setStatus("credit_review");    // Change the Order Status and Order State
                $order->addStatusToHistory($order->getStatus(), 'Your Credit is being Reviewed');     // Add Comment to Order History
                $order->save();     // Save the Changes in Order Status & History
                # incomplete...
                // echo $order->getState();
                // echo $order->getStatus();
                // $histories = $order->getStatusHistories();
                // $latestHistoryComment = array_pop($histories);
                // echo $comment = $latestHistoryComment->getComment();
                // echo " ";
            }
            else {
                $order->setState("processing")->setStatus("processing");    // Change the Order Status and Order State
                // $order->addStatusToHistory($order->getStatus(), 'Your Credit is being Reviewed');     // Add Comment to Order History
                $order->save();     // Save the Changes in Order Status & History
                # incomplete...

            }
            $this->_arInvoiceHelper->webDownPayment('532414', '20', '124', '13245');
            
            $this->_arInvoiceHelper->goSupplyInvoice('12457', 'Sooraj', 'Sathyan', 'sooraj@dyode.com');
            break;
        }

        // $orderId = 1;
        // $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        // $order = $objectManager->create('\Magento\Sales\Model\Order') ->load($orderId);
        // $order->setState("processing")->setStatus("estimate_issue");
        // $order->save();

        // echo $order->getIncrementId();
        // echo $order->getState();
        // echo $order->getStatus();  
            
        echo " ";
        // $customerId = "500-8555";
        // $string = "hello";
        // $int = 123;
        // $float = 405.20;
        // $boolean = true;
        // $inputArray = array(
        //     'CustomerID' => '658138',
        //     'CreateDate' => '2018-06-08',
        //     'CreateTime' => '2:31:23',
        //     'WebReference' => $string,
        //     'SubTotal' => '$199.99',
        //     'TaxAmount' => '$18.50',
        //     'DestinationZip' => '93906',
        //     'ShipCharge' => '$15.28',
        //     'ShipDescription' => 'United Parcel Service',
        //     'DownPmt' => '0',
        //     'EmpID' => $string,
        //     'DiscountAmount' => '$15.00',
        //     'DiscountDescription' => $string,
        //     'ShippingDiscount' => '$0',
        //     'Detail' => array(
        //         'TEstLine' => array(
        //             'ItemType' => $string,
        //             'Item_ID' => $string,
        //             'Item_Name' => $string,
        //             'Model' => 'Apple',
        //             'ItemSet' => 'Watches',
        //             'Qty' => 1,
        //             'Price' => '199.99',
        //             'Cost' => '199.99',
        //             'Taxable' => $string,
        //             'WebVendor' => 2,
        //             'From' => $string,
        //             'PickUp' => 'false',
        //             'OrdItemID' => $int,
        //             'Tax_Amt' => '$18.50',
        //             'Tax_Rate' => '9.25'  
        //         )
        //     )
        // );
        // $inputArray1 = array(
        //     'CustomerID' => '658138',
        //     'CreateDate' => '2018-06-08',
        //     'CreateTime' => '2:31:23',
        //     'WebReference' => $string,
        //     'SubTotal' => '$199.99',
        //     'TaxAmount' => '$18.50',
        //     'DestinationZip' => '93906',
        //     'ShipCharge' => '$15.28',
        //     'ShipDescription' => 'United Parcel Service',
        //     'DownPmt' => '0',
        //     'EmpID' => $string,
        //     'SubAcct' => $string,
        //     'NoOfPmts' => $string,
        //     'DueDate' => $string,
        //     'RegAcctInfo' => $string,
        //     'DiscountAmount' => '$15.00',
        //     'DiscountDescription' => $string,
        //     'ShippingDiscount' => '$0',
        //     'Detail' => array(
        //         'TEstLine' => array(
        //             'ItemType' => $string,
        //             'Item_ID' => $string,
        //             'Item_Name' => $string,
        //             'Model' => 'Apple',
        //             'ItemSet' => 'Watches',
        //             'Qty' => 1,
        //             'Price' => '199.99',
        //             'Cost' => '199.99',
        //             'Taxable' => $string,
        //             'WebVendor' => 2,
        //             'From' => $string,
        //             'PickUp' => 'false',
        //             'OrdItemID' => $int,
        //             'Tax_Amt' => '$18.50',
        //             'Tax_Rate' => '9.25'
        //         )
        //     )
        // );
        
        // $hasPaymentPlan = false;
        
        // if (!$hasPaymentPlan) {
        //     // Creating Invoice using API CreateInvoiceRev
        //     $createInvoiceResponse = $this->_arInvoiceHelper->createInvoiceRev($inputArray);
        // }
        // else {
        //     // Creating Invoice using API CreateInvoiceReg
        //     $createInvoiceResponse = $this->_arInvoiceHelper->createInvoiceReg($inputArray1);
        // }

        // // Validating the Payment Method
        // if (strpos($createInvoiceResponse, 'ERROR') !== false) {
        //     echo 'ERROR';
            
        // } else {
        //     echo 'NOT ERROR';
        // }
        // echo " ";

        $orderStatusOptions = $this->getStatusOptions();
        // var_dump($orderStatusOptions);

        

        die();
        // $collection1 = $collection = $this->_orderCollectionFactory->create()->addAttributeToSelect('*');
        
        // 
        // return $collection;
    }

    
    /**
     * Get status options
     *
     * @return array
     */
    public function getStatusOptions()
    {       
        $options = $this->_statusCollectionFactory->create()->toOptionArray();        
        return $options;
    }
}
