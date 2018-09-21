<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */
namespace Dyode\ArInvoice\Model;

use Dyode\ArInvoice\Helper\Data;
use Magento\Sales\Model\Order;

class ArInvoice extends \Magento\Framework\Model\AbstractModel
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
     * @var \Dyode\Customerstatus\Helper\Data $customerStatusHelper
     **/
    protected $_customerStatusHelper;

    /**
     * @var \Dyode\Signifyd\Model\Signifyd $signifydModel
     */
    protected $_signifydModel;

    /**
     * Construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory
     * @param \Dyode\ArInvoice\Helper\Data $arInvoiceHelper
     * @param \Dyode\Customerstatus\Helper\Data $customerStatusHelper
     * @param \Dyode\Signifyd\Model\Signifyd $signifydModel
     * @param \Magento\Framework\Registry $data
     */
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Sales\Model\OrderRepository $orderRepository,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Sales\Model\ResourceModel\Order\Status\CollectionFactory $statusCollectionFactory,
        \Dyode\ArInvoice\Helper\Data $arInvoiceHelper,
        \Dyode\Customerstatus\Helper\Data $customerStatusHelper,
        \Dyode\Signifyd\Model\Signifyd $signifydModel,
        \Magento\Framework\Registry $data
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_pageFactory = $pageFactory;
        $this->_statusCollectionFactory = $statusCollectionFactory;
        $this->_arInvoiceHelper = $arInvoiceHelper;
        $this->_customerStatusHelper = $customerStatusHelper;
        $this->_signifydModel = $signifydModel;
		return parent::__construct($context, $data);
	}

    /**
     * Create AR Invoice by Order Id
     */
    public function createInvoice($orderId)
    {
        $order = $this->getOrderInfo($orderId);
        # Getting the Payment Method
        $paymentMethod = $order->getPayment()->getMethod();
        /**
         * Validating the Payment Method
         */
        if (strpos($paymentMethod, 'authorizenet') !== false) {
            echo 'Authorize.net';
            // Signify_Required
            $signifyRequired = True;    # Setting Signify Required = True

            # Loading Transactional Details
            $amountPaid = $order->getPayment()->getAmountPaid();
            $orderTotal = $order->getGrandTotal();
            if ($amountPaid >= $orderTotal) {
                # code...
                $cashAmount = $amountPaid;
                $accountNumber = '500-8555';
                $orderType = "full_credit_card";
            }
            else {
                # code...
                $cashAmount = $amountPaid;
                $orderType = "partial_credit_card";
            }
        }
        else {
            $orderType = "full_curacao_credit";     # Setting Order Type = Full Curacao Credit
        }
        if (empty($accountNumber)) {
            # code...
            $accountNumber = $order->getCustomerId();
        }
        # Validating the Account Number
        $accountNumber = $this->_arInvoiceHelper->validateAccountNumber($accountNumber);

        if ($accountNumber == "500-8555" and $orderType == "full_credit_card") {
            # Getting the Check Customer Status
            $customerStatusResponse = $this->_customerStatusHelper->checkCustomerStatus($order, '54421729');
            $customerStatus = json_decode($customerStatusResponse);
        }
        else {
            $accountNumber;    // Formatted Account Number
        }

        if ($signifyRequired == True) {
            # code... Signify Score
            $this->_signifydModel->processSignifyd($order->getIncrementId());
        }
        # code... incomplete ... Prepare Order Items
        $itemsStoreLocation = $this->prepareOrderItems($orderId);
        foreach ($itemsStoreLocation as $itemId => $storeId) {
            # code...
            # Set Store Id to Order Item
        }

        $createdDate = date('Y-m-d\Th:i:s', strtotime($order->getData("created_at")));
        $createdTime = date('h:i A', strtotime($order->getData("created_at")));
        $subTotal = $order->getSubTotal() + $order->getShippingAmount() + $order->getDiscountAmount();
        $taxAmount = $order->getTaxAmount();
        $postCode = $order->getShippingAddress()->getPostCode();
        $shippingAmount = $order->getShippingAmount();
        $shippingDescription = $order->getShippingDescription();
        $amountPaid = $order->getPayment()->getAmountPaid();
        $orderTotal = $order->getGrandTotal();
        if ($orderType == "full_credit_card" or $orderType == "full_curacao_credit") {
            $downPaymentAmount = '0';
        }
        else {
            $downPaymentAmount = $amountPaid;
        }
        $discountAmount = $order->getDiscountAmount();
        $discountDescription = $order->getData("discount_description");
        $shippingDiscount = $order->getShippingDiscountAmount();
        $storeId = $order->getStoreId();
        # Assigning values to input Array
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
            'DownPmt' => $downPaymentAmount,
            'EmpID' => '', # incomplete...
            'DiscountAmount' => $discountAmount,
            'DiscountDescription' => $discountDescription,
            'ShippingDiscount' => $shippingDiscount
        );
        $items = array();
        // Assigning values to input Array
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
            array_push($items, array(
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
        $inputArray["items"] = $items;
        # dummy values
        $inputArray = array(
            "CustomerID" => "53208833",
            "CreateDate" => "2018-07-19",
            "CreateTime" => "14:00:00",
            "WebReference" => "123456789",
            "SubTotal" => 30,
            "TaxAmount" => 2,
            "DestinationZip" => "91801",
            "ShipCharge" => 0,
            "ShipDescription" => "description here",
            "DownPmt" => 0,
            "EmpID" => "BPZ",
            "DiscountAmount" => 0,
            "DiscountDescription" => "",
            "ShippingDiscount" => 0,
            "items" =>  array(
                array(
                    "itemtype" => "CUR",
                    "item_id" => "09A-RA3-RS16FT5050RB",
                    "item_name" => "RACE SPORT 16FT RGB ST",
                    "model" => "RS16FT5050RB",
                    "itemset" => false,
                    "qty" => 1,
                    "price" => 10,
                    "cost" => 5,
                    "taxable" => false,
                    "webvendor" => 0,
                    "from" => "01",
                    "pickup" => true,
                    "orditemid" => 0,
                    "tax_amt" => 1,
                    "tax_rate" => 0
                ),
                array(
                    "itemtype" => "CUR",
                    "item_id" => "32O-285-42LB5600",
                    "item_name" => "LG 42 1080P 60HZ LED",
                    "model" => "42LB5600",
                    "itemset" => false,
                    "qty" => 1,
                    "price" => 20,
                    "cost" => 10,
                    "taxable" => false,
                    "webvendor" => 0,
                    "from" => "01",
                    "pickup" => true,
                    "orditemid" => 0,
                    "tax_amt" => 1,
                    "tax_rate" => 0
                )
            )
        );

        $createInvoiceResponse = $this->_arInvoiceHelper->createRevInvoice($inputArray);    # Creating Invoice using API CreateInvoiceRev
        /**
         * Create Invoice Response Validation
         */
        if ($createInvoiceResponse->OK != true) {   # Create Invoice Response is false
            # code...
            echo 'ERROR';
            $order->setState("processing")->setStatus("estimate_issue");    # Change the Order Status and Order State
            $order->addStatusToHistory($order->getStatus(), 'Estimate not Issued');     # Add Comment to Order History
            $order->save();     # Save the Changes in Order Status & History
            # incomplete...
        }
        else {  # Create Invoice Response is true
            $estimateNumber = $invoiceNumber = $createInvoiceResponse->DATA->INV_NO;    # Save Estimate Number in Order
            /**
             * Customer Status Validation
             */
            if ($customerStatus['customerstatus'] == False || $customerStatus["addressmismatch"] == True || $customerStatus["soft"] == True) {
                $order->setState("payment_review")->setStatus("credit_review");    # Change the Order Status and Order State
                $order->addStatusToHistory($order->getStatus(), 'Your Credit is being Reviewed');     # Add Comment to Order History
                $order->save();     # Save the Changes in Order Status & History
                # incomplete...
                # Notify Customer
                # Notify Credit Department to Review
                // echo $order->getState();
                // echo $order->getStatus();
                // $histories = $order->getStatusHistories();
                // $latestHistoryComment = array_pop($histories);
                // echo $comment = $latestHistoryComment->getComment();
                // echo " ";
            }
            else {
                $order->setState("processing")->setStatus("processing");    # Change the Order Status and Order State
                $order->save();     # Save the Changes in Order Status & History
                $referId = $orderId;
                # dummy data
                $accountNumber = "53208833";
                $downPaymentAmount = 1.5;
                $invoiceNumber = "ZEP58P4";
                $referId = "refer#1";
                # Web Down Payment API
                $webDownPaymentResponse = $this->_arInvoiceHelper->webDownPayment($accountNumber, $downPaymentAmount, $invoiceNumber, $referId);
                $customerFirstName = $order->getCustomerFirstname();
                $customerLastName = $order->getCustomerLastname();
                $customerEmail = $order->getCustomerEmail();
                # dummy data
                $invoiceNumber = "ZEP58P6";
                $customerFirstName = "Joe";
                $customerLastName = "Smith";
                $customerEmail = "joe@smith.com";
                # Supply Invoice API
                $supplyInvoiceResponse = $this->_arInvoiceHelper->supplyInvoice($invoiceNumber, $customerFirstName, $customerLastName, $customerEmail);
                # incomplete...
            }
        }
        return;
    }

    /**
     * Get Order Info by Order Id
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

    /**
     * Prepare Order Items for AR Invoice
     *
     * @return array
     */
    public function prepareOrderItems($orderId)
    {
        /**
         * Initialize Order Items Location & Grouped Items Array
         */
        $orderItemsLocation = array();
        $groupedItemsLocation = array();
        // $orderItemsLocation = array("134"=>"01", "135"=>"16");
        /**
         * Get Order Info by Order Id
         */
        $order = $this->_orderRepository->get($orderId);
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getParentItemId() == null) {
                $orderItemsLocation[$orderItem->getItemId()] = $this->_arInvoiceHelper->assignInventoryLocation($orderItem);
                // if ($orderItemsLocation[$orderItem->getItemId()] == "k") {
                //     # code...
                //     $orderItems[$orderItem->getItemId()] = array(
                //         "ProductId" => $orderItem->getProductId(),
                //         "ItemQty" => $orderItem->getQtyOrdered()
                //     );
                //     unset($orderItemsLocation[$orderItem->getItemId]);
                // }
            }
        }

        if (!empty($orderItems)) {
            # code...
            $groupedItemsLocation = $this->_arInvoiceHelper->getGroupedLocation($order,$orderItems);
            $orderItemsLocation = $orderItemsLocation + $groupedItemsLocation;
            ksort($orderItemsLocation);
        }
        print_r($orderItemsLocation);
        die();
        // $orderItemsLocation = array("133"=>"01", "134"=>"k", "135"=>"09", "136"=>"k", "137"=>"k");
        return $orderItemsLocation;
    }
}
