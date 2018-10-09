<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
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
     * @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var \Magento\Catalog\Model\ProductRepository $productRepository
     */
    protected $_productRepository;

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
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
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
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Registry $data
    ) {
        $this->_orderCollectionFactory = $orderCollectionFactory;
        $this->_orderRepository = $orderRepository;
        $this->_pageFactory = $pageFactory;
        $this->_statusCollectionFactory = $statusCollectionFactory;
        $this->_arInvoiceHelper = $arInvoiceHelper;
        $this->_customerStatusHelper = $customerStatusHelper;
        $this->_signifydModel = $signifydModel;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_productRepository = $productRepository;
		return parent::__construct($context, $data);
	}

    /**
     * Create AR Invoice by Order Id
     *
     * @return void
     */
    public function createInvoice($orderId)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . "/var/log/ordercancellation.log");
		$logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $order = $this->getOrderInfo($orderId);
        # Getting the Payment Method
        $paymentMethod = $order->getPayment()->getMethod();

        // $signifyRequired = false;
        /**
         * Validating the Payment Method
         */
        if (strpos($paymentMethod, 'authorizenet') !== false) {
            // Signify_Required
            // $signifyRequired = True;    # Setting Signify Required = True

            # Loading Transactional Details
            $amountPaid = $order->getPayment()->getAmountPaid();
            $orderTotal = $order->getGrandTotal();
            if ($amountPaid >= $orderTotal) {
                $cashAmount = $amountPaid;
                $accountNumber = '500-8555';
                $orderType = "full_credit_card";
            } else {
                $cashAmount = $amountPaid;
                $orderType = "partial_credit_card";
            }
        } else {
            $orderType = "full_curacao_credit";     # Setting Order Type = Full Curacao Credit
        }
        if (empty($accountNumber)) {
            # code...
            $customerId = $order->getCustomerId();
            $customer = $this->_customerRepositoryInterface->getById($customerId);
            $accountNumber = $customer->getCustomAttribute("curacaocustid")->getValue();
        }
        # Validating the Account Number
        $accountNumber = $this->_arInvoiceHelper->validateAccountNumber($accountNumber);

        if ($accountNumber == "500-8555" and $orderType == "full_credit_card") {
            # Getting the Check Customer Status
            $customerStatusResponse = $this->_customerStatusHelper->checkCustomerStatus($order, '54421729');
            $customerStatus = json_decode($customerStatusResponse);
        }

        // if ($signifyRequired == True) {
        //     # Signify Score
        //     $this->_signifydModel->processSignifyd($order->getIncrementId());
        // }

        # Prepare Order Items
        $itemsStoreLocation = $this->prepareOrderItems($orderId);

        # Getting Order Values
        $incrementId = $order->getIncrementId();
        $createdDate = date('Y-m-d', strtotime($order->getData("created_at")));
        $createdTime = date('h:i:s', strtotime($order->getData("created_at")));
        $subTotal = $order->getSubTotal() + $order->getShippingAmount() + $order->getDiscountAmount();
        $taxAmount = $order->getTaxAmount();
        $postCode = $order->getShippingAddress()->getPostCode();
        $shippingAmount = $order->getShippingAmount();

        $shippingDescription = ($order->getShippingDescription() !== null) ? $order->getShippingDescription() : "";

        // if ($order->getShippingDescription() !== null) {
        //     $shippingDescription = $order->getShippingDescription();
        // } else {
        //     $shippingDescription = "";
        // }

        $amountPaid = $order->getPayment()->getAmountPaid();
        $orderTotal = $order->getGrandTotal();

        $downPaymentAmount = ($orderType == "full_credit_card" or $orderType == "full_curacao_credit") ? '0' : $amountPaid;

        // if ($orderType == "full_credit_card" or $orderType == "full_curacao_credit") {
        //     $downPaymentAmount = '0';
        // } else {
        //     $downPaymentAmount = $amountPaid;
        // }

        $discountAmount = $order->getDiscountAmount();

        $discountDescription = ($order->getData("discount_description") !== null) ? $order->getData("discount_description") : "";

        // if ($order->getData("discount_description") !== null) {
        //     $discountDescription = $order->getData("discount_description");
        // } else {
        //     $discountDescription = "";
        // }

        $shippingDiscount = $order->getShippingDiscountAmount();

        // $accountNumber = "53208833";
        # Assigning values to input Array
        $inputArray = array(
            "CustomerID" => $accountNumber,
            "CreateDate" => $createdDate,
            "CreateTime" => $createdTime,
            "WebReference" => $incrementId,
            "SubTotal" => $subTotal,
            "TaxAmount" => (double)$taxAmount,
            "DestinationZip" => $postCode,
            "ShipCharge" => (double)$shippingAmount,
            "ShipDescription" => $shippingDescription,
            "DownPmt" => (double)$downPaymentAmount,
            "EmpID" => "",
            "DiscountAmount" => (double)$discountAmount,
            "DiscountDescription" => $discountDescription,
            "ShippingDiscount" => (double)$shippingDiscount,
        );
        $items = array();
        // Assigning values to input Array items
        foreach ($order->getAllItems() as $item)
        {
            $product = $this->_productRepository->getById($item->getProductId()); 
            $itemSku = $item->getSku();
            $itemName = $item->getName();
            $itemQty = $item->getQtyOrdered();
            $itemPrice = $item->getPrice();
            $itemCost = $item->getBasePrice();

            $pickup = ($item->getData('delivery_type') == 1) ? true : false;

            // if ($item->getData('delivery_type') == 1) {
            //     $pickup = true;
            // } else {
            //     $pickup = false;
            // }
            $taxable = ($item->getTaxAmount() > 0) ? true : false;

            // if ($item->getTaxAmount() > 0) {
            //     $taxable = true;
            // } else {
            //     $taxable = false;
            // }

            $itemId = $item->getId();
            $itemTaxAmount = $item->getTaxAmount();
            $itemTaxRate = $item->getTaxPercent();

            $itemSet = ($product->getData('set')) ? true : false;

            // if ($product->getData('set')) {
            //     $itemSet = true;
            // } else {
            //     $itemSet = false;
            // }

            $vendorId = $product->getData('vendorid');

            $itemType = ($product->getData('vendorid') == 2139) ? "CUR" : "";

            // if ($product->getData('vendorid') == 2139) {
            //     $itemType = "CUR";
            // } else {
            //     $itemType = "";
            // }

            $explodeItemSku = explode("-", $itemSku);

            array_push($items, array(
                    "itemtype" => $itemType,
                    "item_id" => $itemSku,
                    "item_name" => $itemName,
                    "model" => end($explodeItemSku),
                    "itemset" => $itemSet,
                    "qty" => (int)$itemQty,
                    "price" => (double)$itemPrice,
                    "cost" => (double)$itemCost,
                    "taxable" => $taxable,
                    "webvendor" => (int)$vendorId,
                    "from" => $itemsStoreLocation[$itemId],
                    "pickup" => $pickup,
                    "orditemid" => (int)$itemId,
                    "tax_amt" => (double)$itemTaxAmount,
                    "tax_rate" => (double)$itemTaxRate
                )
            );
        }
        $inputArray["items"] = $items;

        // # dummy values
        // $inputArray = array(
        //     "CustomerID" => "53208833",
        //     "CreateDate" => $createdDate,
        //     "CreateTime" => $createdTime,
        //     "WebReference" => $incrementId,
        //     "SubTotal" => $subTotal,
        //     "TaxAmount" => (double)$taxAmount,
        //     "DestinationZip" => $postCode,
        //     "ShipCharge" => (double)$shippingAmount,
        //     "ShipDescription" => $shippingDescription,
        //     "DownPmt" => (double)$downPaymentAmount,
        //     "EmpID" => "",
        //     "DiscountAmount" => (double)$discountAmount,
        //     "DiscountDescription" => $discountDescription,
        //     "ShippingDiscount" => (double)$shippingDiscount,
        //     "items" =>  array(
        //         array(
        //             "itemtype" => $itemType,
        //             "item_id" => $itemSku,
        //             "item_name" => $itemName,
        //             "model" => end($explodeItemSku),
        //             "itemset" => $itemSet,
        //             "qty" => (int)$itemQty,
        //             "price" => (double)$itemPrice,
        //             "cost" => (double)$itemCost,
        //             "taxable" => $taxable,
        //             "webvendor" => (int)$vendorId,
        //             "from" => $itemsStoreLocation[$itemId],
        //             "pickup" => $pickup,
        //             "orditemid" => (int)$itemId,
        //             "tax_amt" => (double)$itemTaxAmount,
        //             "tax_rate" => (double)$itemTaxRate
        //         )
        //     )
        // );

        $createInvoiceResponse = $this->_arInvoiceHelper->createRevInvoice($inputArray);    # Creating Invoice using API CreateInvoiceRev

        if (empty($response)) {
			$logger->info("Order Id : " . $order->getIncrementId());
			$logger->info("API Response not Found.");
			throw new Exception("API Response not Found", 1);
        }

        /**
         * Create Invoice Response Validation
         */
        if ($createInvoiceResponse->OK != true) {   # Create Invoice Response is false
            $order->setState("processing")->setStatus("estimate_issue");    # Change the Order Status and Order State
            $order->addStatusToHistory($order->getStatus(), 'Estimate not Issued');     # Add Comment to Order History
            $order->save();     # Save the Changes in Order Status & History
            // Logger
            $logger->info("Order Id : " . $order->getIncrementId());
            $logger->info($response->INFO);
        } else {  # Create Invoice Response is true
            $estimateNumber = $invoiceNumber = $createInvoiceResponse->DATA->INV_NO;    # Save Estimate Number in Order
            $order->setData('estimatenumber', $estimateNumber);
            $order->addStatusToHistory($order->getStatus(), 'Estimate Number: ' . $estimateNumber );     # Add Comment to Order History
            $order->save();
            /**
             * Customer Status Validation
             */
            if ($customerStatus['customerstatus'] == False || $customerStatus["addressmismatch"] == True || $customerStatus["soft"] == True) {
                $order->setState("payment_review")->setStatus("credit_review");    # Change the Order Status and Order State
                $order->addStatusToHistory($order->getStatus(), 'Your Credit is being Reviewed');     # Add Comment to Order History
                $order->save();     # Save the Changes in Order Status & History
                # Notify Customer  - incomplete...
                # Notify Credit Department to Review  - incomplete...
            } else {
                $order->setState("processing")->setStatus("processing");    # Change the Order Status and Order State
                $order->save();     # Save the Changes in Order Status & History
                $referId = $incrementId;

                # Web Down Payment API
                $webDownPaymentResponse = $this->_arInvoiceHelper->webDownPayment($accountNumber, $downPaymentAmount, $invoiceNumber, $referId);
                /**
                 * Not Required
                 */
                /*
                    $customerFirstName = $order->getCustomerFirstname();
                    $customerLastName = $order->getCustomerLastname();
                    $customerEmail = $order->getCustomerEmail();
                    # Supply Invoice API
                    $supplyInvoiceResponse = $this->_arInvoiceHelper->supplyInvoice($invoiceNumber, $customerFirstName, $customerLastName, $customerEmail);
                */
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
        /**
         * Get Order Info by Order Id
         */
        $order = $this->_orderRepository->get($orderId);
        foreach ($order->getAllItems() as $orderItem) {
            if ($orderItem->getParentItemId() == null) {
                $orderItemsLocation[$orderItem->getItemId()] = $this->_arInvoiceHelper->assignInventoryLocation($orderItem);
                if ($orderItemsLocation[$orderItem->getItemId()] == "k") {
                    $orderItems[$orderItem->getItemId()] = array(
                        "ProductId" => $orderItem->getProductId(),
                        "ItemQty" => $orderItem->getQtyOrdered()
                    );
                    unset($orderItemsLocation[$orderItem->getItemId()]);
                }
            }
        }
        if (!empty($orderItems)) {
            $groupedItemsLocation = $this->_arInvoiceHelper->getGroupedLocation($order,$orderItems);
            $orderItemsLocation = $orderItemsLocation + $groupedItemsLocation;
            ksort($orderItemsLocation);
        }
        return $orderItemsLocation;
    }
}
