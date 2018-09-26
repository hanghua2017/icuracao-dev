<?php
/**
 * @category  Dyode
 * @package   Dyode_ArInvoice
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
     */
    public function createInvoice($orderId)
    {
        $order = $this->getOrderInfo($orderId);
        # Getting the Payment Method
        $paymentMethod = $order->getPayment()->getMethod();
        # signifyRequired False as default 
        $signifyRequired = false;
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
            } else {
                # code...
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
        echo $signifyRequired;

        if ($signifyRequired == True) {
            # Signify Score
            $this->_signifydModel->processSignifyd($order->getIncrementId());
        }
        echo "Hello";
        die();
        # code... incomplete ... Prepare Order Items
        $itemsStoreLocation = $this->prepareOrderItems($orderId);
        print_r($itemsStoreLocation);
        die();
        foreach ($itemsStoreLocation as $itemId => $storeId) {
            # code...
            # Set Store Id to Order Item
        }
        
        # Getting Order Values
        $incrementId = $order->getIncrementId();
        $createdDate = date('Y-m-d', strtotime($order->getData("created_at")));
        $createdTime = date('h:i:s', strtotime($order->getData("created_at")));
        $subTotal = $order->getSubTotal() + $order->getShippingAmount() + $order->getDiscountAmount();
        $taxAmount = $order->getTaxAmount();
        $postCode = $order->getShippingAddress()->getPostCode();
        $shippingAmount = $order->getShippingAmount();
        if ($order->getShippingDescription() !== null) {
            $shippingDescription = $order->getShippingDescription();
        } else {
            $shippingDescription = "";
        }
        $amountPaid = $order->getPayment()->getAmountPaid();
        $orderTotal = $order->getGrandTotal();
        if ($orderType == "full_credit_card" or $orderType == "full_curacao_credit") {
            $downPaymentAmount = '0';
        } else {
            $downPaymentAmount = $amountPaid;
        }
        $discountAmount = $order->getDiscountAmount();
        if ($order->getData("discount_description") !== null) {
            $discountDescription = $order->getData("discount_description");
        } else {
            $discountDescription = "";
        }
        $shippingDiscount = $order->getShippingDiscountAmount();

        $accountNumber = "53208833";
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
        // Assigning values to input Array
        foreach ($order->getAllItems() as $item)
        {
            echo $item->getData('delivery_type');
            
            $product = $this->_productRepository->getById($item->getProductId()); 
            $itemType = $item->getProductType();
            $itemSku = $item->getSku();
            $itemName = $item->getName();
            $itemQty = $item->getQtyOrdered();
            $itemPrice = $item->getPrice();
            $itemCost = $item->getBasePrice();
            if ($item->getData('delivery_type') == 1) {
                $pickup = true;
            } else {
                $pickup = false;
            }
            if ($item->getTaxAmount() > 0) {
                $taxable = true;
            } else {
                $taxable = false;
            }

            $itemId = $item->getId();
            $itemTaxAmount = $item->getTaxAmount();
            $itemTaxRate = $item->getTaxPercent();

            if ($product->getData('set')) {
                $itemSet = true;
            } else {
                $itemSet = false;
            }

            $vendorId = $product->getData('vendorid');

            if ($product->getData('vendorid') == 2139) {
                $itemType = "CUR";
            } else {
                $itemType = "";
            }

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
                "taxable" => $taxable, # incomplete
                "webvendor" => (int)$vendorId,
                "from" => "01", # incomplete...
                "pickup" => $pickup, # incomplete...
                "orditemid" => (int)$itemId,
                "tax_amt" => (double)$itemTaxAmount,
                "tax_rate" => (double)$itemTaxRate
                )
            );
        }

        $inputArray["items"] = $items;
        echo "<pre>";
        print_r(json_encode($inputArray));

        # dummy values
        $inputArray = array(
            "CustomerID" => "53208833",
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
            "items" =>  array(
                array(
                    "itemtype" => $itemType,
                    "item_id" => $itemSku,
                    "item_name" => $itemName,
                    "model" => end($explodeItemSku),
                    "itemset" => $itemSet,
                    "qty" => (int)$itemQty,
                    "price" => (double)$itemPrice,
                    "cost" => (double)$itemCost,
                    "taxable" => false,
                    "webvendor" => (int)$vendorId,
                    "from" => "01",
                    "pickup" => $pickup,
                    "orditemid" => (int)$itemId,
                    "tax_amt" => (double)$itemTaxAmount,
                    "tax_rate" => (double)$itemTaxRate
                )
            )
        );
        echo "<br>";
        print_r(json_encode($inputArray));
        
        die();
        $createInvoiceResponse = $this->_arInvoiceHelper->createRevInvoice($inputArray);    # Creating Invoice using API CreateInvoiceRev
        /**
         * Create Invoice Response Validation
         */
        if ($createInvoiceResponse->OK != true) {   # Create Invoice Response is false
            # code...
            $order->setState("processing")->setStatus("estimate_issue");    # Change the Order Status and Order State
            $order->addStatusToHistory($order->getStatus(), 'Estimate not Issued');     # Add Comment to Order History
            $order->save();     # Save the Changes in Order Status & History
        } else {  # Create Invoice Response is true
            $estimateNumber = $invoiceNumber = $createInvoiceResponse->DATA->INV_NO;    # Save Estimate Number in Order
            $order->setData('estimatenumber', $estimateNumber);
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
                
                $customerFirstName = $order->getCustomerFirstname();
                $customerLastName = $order->getCustomerLastname();
                $customerEmail = $order->getCustomerEmail();
                # Supply Invoice API
                $supplyInvoiceResponse = $this->_arInvoiceHelper->supplyInvoice($invoiceNumber, $customerFirstName, $customerLastName, $customerEmail);
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
