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
        $order = $this->getOrderInfo($orderId);
        $accountNumber = $order->getCustomerId();
        // Getting the Payment Method
        $paymentMethod = $order->getPayment()->getMethod();
        // Validating the Payment Method
        if (strpos($paymentMethod, 'authorizenet') !== false) {
            echo 'Authorize.net';
            // Signify_Required
            $signifyRequired = True;    //Setting Signify Required = True
            
            # Loading Transactional Details
            $amountPaid = $order->getPayment()->getAmountPaid();
            $orderTotal = $order->getGrandTotal();
            if ($amountPaid >= $orderTotal || $accountNumber == '500-0000') {
                # code...
                $cashAmount = $amountPaid;
                $accountNumber = '500-8555';
                $orderType = "full-credit";
            }
            else {
                # code...
                $cashAmount = $amountPaid;
                $orderType = "partial-credit";
            }
        }
        else {
            echo 'false';
            $orderType = "full-curacao-credit";     // Setting Order Type = Full Curacao Credit
        }
        
        echo $orderType;
        // Validating the Account Number
        if (empty($accountNumber)) {
            $accountNumber = $this->_arInvoiceHelper->validateAccountNumber($accountNumber); 
        }
        
        // $accountNumber = "000-0001";
        // Regular expression for Account Numbers
        $regex = "/^[0-9]{3}-[0-9]{4}$/";

        if (!preg_match($regex, $accountNumber)) {
            $soapClient = $this->_arInvoiceHelper->setSoapClient();
            $customerStatusResponse = $soapClient->checkCustomerStatus($accountNumber);
            // var_dump($customerStatusResponse);
            # code... incomplete ... Check Customer Status
        }
        else {
            $accountNumber;    // Formatted Account Number
        }
        
        if ($signifyRequired == True) {
            # code... incomplete ... Get Signify Score
        }
        else {
            # code... incomplete ... Prepare Order Items
        }
        
        $createdDate = date('Y-m-d\Th:i:s', strtotime($order->getData("created_at")));
        $createdTime = date('h:i A', strtotime($order->getData("created_at")));
        $subTotal = $order->getSubTotal() + $order->getShippingAmount() + $order->getDiscountAmount();
        $taxAmount = $order->getTaxAmount();
        $postCode = $order->getShippingAddress()->getPostCode();
        $shippingAmount = $order->getShippingAmount();
        $shippingDescription = $order->getShippingDescription();
        $discountAmount = $order->getDiscountAmount();
        $discountDescription = $order->getData("discount_description");
        $shippingDiscount = $order->getShippingDiscountAmount();
        $storeId = $order->getStoreId();
    
        // Testing the API 
        $accountNumber = "157514";
        $soapClient = $this->_arInvoiceHelper->setSoapClient();
        $result = $soapClient->isCustomerActive($accountNumber);
        // var_dump($result);

        // dummy value
        $hasPaymentPlan = false;
        if (!$hasPaymentPlan) {
            // Assigning values to input Array
            $inputArray = array(
                'CustomerID' => $accountNumber,
                'CreateDate' => $createdDate,
                'CreateTime' => $createdTime,
                'WebReference' => $orderId,
                'SubTotal' => $subTotal,
                'TaxAmount' => $taxAmount,
                'DestinationZip' => $postCode,
                'ShipCharge' => $shippingAmount,
                'ShipDescription' => $shippingDescription,
                'DownPmt' => '', # incomplete...
                'EmpID' => '', # incomplete...
                'DiscountAmount' => $discountAmount,
                'DiscountDescription' => $discountDescription,
                'ShippingDiscount' => $shippingDiscount
            );
            foreach ($order->getAllItems() as $item)
            {   
                $itemType = $item->getProductType();
                $itemSku = $item->getSku();
                $itemName = $item->getName();
                $itemQty = $item->getQtyOrdered();
                $itemPrice = $item->getPrice();
                $itemCost = $item->getBasePrice();
                $itemId = $item->getId();
                $itemTaxAmount = $item->getTaxAmount();
                $itemTaxRate = $item->getTaxPercent();
                // print_r($item->getData());
                echo " ";
                $inputArray['Detail'] = array(
                    'TEstLine' => array(
                        'ItemType' => $itemType,
                        'Item_ID' => $itemSku,
                        'Item_Name' => $itemName,
                        'Model' => '', # incomplete...
                        'ItemSet' => '', # incomplete...
                        'Qty' => $itemQty,
                        'Price' => $itemPrice,
                        'Cost' => $itemCost,
                        'Taxable' => '', # incomplete
                        'WebVendor' => '', # incomplete...
                        'From' => $storeId,
                        'PickUp' => '', # incomplete...
                        'OrdItemID' => $itemId,
                        'Tax_Amt' => $itemTaxAmount,
                        'Tax_Rate' => $itemTaxRate  
                    )
                );
            }   
            // Creating Invoice using API CreateInvoiceRev
            $createInvoiceResponse = $this->_arInvoiceHelper->createInvoiceRev($inputArray);
        }
        else {
            // Assigning values to input Array
            $inputArray = array(
                'CustomerID' => $accountNumber,
                'CreateDate' => $createdDate,
                'CreateTime' => $createdTime,
                'WebReference' => $orderId,
                'SubTotal' => $subTotal,
                'TaxAmount' => $taxAmount,
                'DestinationZip' => $postCode,
                'ShipCharge' => $shippingAmount,
                'ShipDescription' => $shippingDescription,
                'DownPmt' => '', # incomplete...
                'EmpID' => '', # incomplete...
                'SubAcct' => '', # incomplete...
                'NoOfPmts' => '', # incomplete...
                'DueDate' => '', # incomplete...
                'RegAcctInfo' => '', # incomplete...
                'DiscountAmount' => $discountAmount,
                'DiscountDescription' => $discountDescription,
                'ShippingDiscount' => $shippingDiscount
            );
            foreach ($order->getAllItems() as $item)
            {   
                $itemType = $item->getProductType();
                $itemSku = $item->getSku();
                $itemName = $item->getName();
                $itemQty = $item->getQtyOrdered();
                $itemPrice = $item->getPrice();
                $itemCost = $item->getBasePrice();
                $itemId = $item->getId();
                $itemTaxAmount = $item->getTaxAmount();
                $itemTaxRate = $item->getTaxPercent();
                // print_r($item->getData());
                // echo " ";
                $inputArray['Detail'] = array(
                    'TEstLine' => array(
                        'ItemType' => $itemType,
                        'Item_ID' => $itemSku,
                        'Item_Name' => $itemName,
                        'Model' => '', # incomplete...
                        'ItemSet' => '', # incomplete...
                        'Qty' => $itemQty,
                        'Price' => $itemPrice,
                        'Cost' => $itemCost,
                        'Taxable' => '', # incomplete
                        'WebVendor' => '', # incomplete...
                        'From' => $storeId,
                        'PickUp' => '', # incomplete...
                        'OrdItemID' => $itemId,
                        'Tax_Amt' => $itemTaxAmount,
                        'Tax_Rate' => $itemTaxRate  
                    )
                );
            }
            // Creating Invoice using API CreateInvoiceReg
            $createInvoiceResponse = $this->_arInvoiceHelper->createInvoiceReg($inputArray1);
        }
        die();

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
                //Assigning demo values 
                $downPaymentAmount = '20'; # incomplete...
                $invoiceNumber = '124'; # incomplete...
                $referId = '13425'; # incomplete...
                $customerFirstName = $order->getCustomerFirstname();
                $customerLastName = $order->getCustomerLastname();
                $customerEmail = $order->getCustomerEmail();
                $this->_arInvoiceHelper->webDownPayment($accountNumber, $downPaymentAmount, $invoiceNumber, $referId);
                $this->_arInvoiceHelper->goSupplyInvoice($invoiceNumber, $customerFirstName, $customerLastName, $customerEmail);
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
