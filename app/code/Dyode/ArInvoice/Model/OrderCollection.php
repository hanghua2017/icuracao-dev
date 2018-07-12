<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */
namespace Dyode\ArInvoice\Model;

use Dyode\ArInvoice\Helper\Data;
use Magento\Sales\Model\Order;

class OrderCollection extends \Magento\Framework\Model\AbstractModel// implements \Magento\Framework\DataObject\IdentityInterface
{   
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     **/
    protected $_orderCollectionFactory;

    /**
     * @var \Magento\Sales\Model\OrderRepository $orderRepository
     **/
    protected $_orderRepository;

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
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
     * @param \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
     * @param \Magento\Framework\Registry $data
     */
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory,
        \Dyode\ArInvoice\Helper\Data $arInvoiceHelper,
        \Magento\Framework\Registry $data
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderRepository = $orderRepository;
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
        $order = $this->getOrderInfo(2);
        // Getting the Payment Method
        $paymentMethod = $order->getPayment()->getMethod();
        // Validating the Payment Method
        if (strpos($paymentMethod, 'authorizenet') !== false) {
            echo 'Authorize.net';
            // Signify_Required
            $signifyRequired = True;    //Setting Signify Required = True
        }
        else {
            echo 'false';
            $orderType = "full-curacao-credit";     // Setting Order Type = Full Curacao Credit
        }
        $accountNumber = $order->getCustomerId();
        // Validating the Account Number
        if (!empty($order->getCustomerId())) {
            $accountNumber = $this->_arInvoiceHelper->validateAccountNumber($accountNumber);
             
        }
        
        // $accountNumber = "000-0001";
        // Regular expression for Account Numbers
        $regex = "/^[0-9]{3}-[0-9]{4}$/";

        if (preg_match($regex, $accountNumber)) {
            $soapClient = $this->_arInvoiceHelper->setSoapClient();
            $customerStatusResponse = $soapClient->checkCustomerStatus($accountNumber);
            // var_dump($customerStatusResponse);
            # code... incomplete ... Check Customer Status
        }
        else {
            echo $accountNumber;    // Formatted Account Number
        }
        
        if ($signifyRequired == True) {
            # code... incomplete ... Get Signify Score
        }
        else {
            # code... incomplete ... Prepare Order Items
        }

        // dummy value
        $hasPaymentPlan = false;
        $customerId = "500-8555";
        $string = "hello";
        $int = 123;
        $float = 405.20;
        $boolean = true;
        $inputArray = array();
        $inputArray1 = array();
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

        
        if (!$hasPaymentPlan) {
            // Creating Invoice using API CreateInvoiceRev
            $createInvoiceResponse = $this->_arInvoiceHelper->createInvoiceRev($inputArray);
        }
        else {
            // Creating Invoice using API CreateInvoiceReg
            $createInvoiceResponse = $this->_arInvoiceHelper->createInvoiceReg($inputArray1);
        }

        // Validating the Create Invoice Response 
        if (strpos($createInvoiceResponse, 'ERROR') !== false) {
            echo 'ERROR';
            $order->setState("processing")->setStatus("estimate_issue");    // Change the Order Status and Order State
            $order->addStatusToHistory($order->getStatus(), 'Estimate not Issued');     // Add Comment to Order History
            $order->save();     // Save the Changes in Order Status & History
        } else {
            echo 'NOT ERROR';
            // Save Estimate# in Order
            $estimateNumber = 123;

            $shippingStreet = '3325 W PICO BLVD APT 9';
            $shippingZip = '90019';
            $addressMismatch = $this->_arInvoiceHelper->validateAddress($order->getCustomerId(), $shippingStreet, $shippingZip);
            $customerStatus = $this->_arInvoiceHelper->isCustomerActive($order->getCustomerId());
            // echo " ";
            if ($customerStatus == "Soft" || $customerStatus == "NO" || addressMismatch == True) {
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
                $order->save();     // Save the Changes in Order Status & History
                $this->_arInvoiceHelper->webDownPayment('532414', '20', '124', '13245');
                $this->_arInvoiceHelper->goSupplyInvoice('12457', 'Sooraj', 'Sathyan', 'sooraj@dyode.com');
                # incomplete...
            }
        }
        echo " ";
    }

    /**
     * Get Sales Order Collection for AR Invoice
     */
    public function getOrderInfo($orderId)
    {
        return $this->_orderRepository->get($orderId);
    }

    /**
     * Get Sales Order Collection for AR Invoice
     */
    public function getSalesOrderCollection()
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $orderCollection = $objectManager->create('\Magento\Sales\Model\ResourceModel\Order\Collection');
        return $orderCollection->load();
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
