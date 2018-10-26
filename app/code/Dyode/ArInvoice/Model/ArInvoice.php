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
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

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
     * @param \Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog,
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService,
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
        \Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
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
        $this->auditLog = $auditLog;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
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
            $amountAuthorized = $order->getPayment()->getAmountAuthorized();
            $orderTotal = $order->getGrandTotal();
            if ($amountAuthorized >= $orderTotal) {
                $cashAmount = $amountAuthorized;
                $accountNumber = '500-8555';
                $orderType = "full_credit_card";
            } else {
                $cashAmount = $amountAuthorized;
                $orderType = "partial_credit_card";
            }
        } else {
            $orderType = "full_curacao_credit";     # Setting Order Type = Full Curacao Credit
        }

        if (empty($accountNumber)) {
            # code...
            $customerId = $order->getCustomerId();

            if (!empty($customerId)) {
                $customer = $this->_customerRepositoryInterface->getById($customerId);
                $accountNumber = (!empty($customer->getCustomAttribute("curacaocustid"))) ?
                    $customer->getCustomAttribute("curacaocustid")->getValue() : null;
            }
        }
        # Validating the Account Number
        $accountNumber = $this->_arInvoiceHelper->validateAccountNumber($accountNumber);

        if (($accountNumber !== "500-8555")) {
            # Getting the Check Customer Status - num 54421729
            $customerStatusResponse = $this->_customerStatusHelper->checkCustomerStatus($order, $accountNumber);
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

        $amountPaid = (!empty($order->getPayment()->getAmountPaid())) ? $order->getPayment()->getAmountPaid()
                : $order->getPayment()->getAmountAuthorized();
        $orderTotal = $order->getGrandTotal();

        $downPaymentAmount = ($orderType == "full_credit_card" or $orderType == "full_curacao_credit") ? '0' : $amountPaid;

        $discountAmount = $order->getDiscountAmount();

        $discountDescription = ($order->getData("discount_description") !== null) ? $order->getData("discount_description") : "";

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
            $taxable = ($item->getTaxAmount() > 0) ? true : false;

            $itemId = $item->getId();
            $itemTaxAmount = $item->getTaxAmount();
            $itemTaxRate = $item->getTaxPercent();

            $itemSet = ($product->getData('set')) ? true : false;

            $vendorId = $product->getData('vendorid');

            $itemType = ($product->getData('vendorid') == 2139) ? "CUR" : "";

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

        $createInvoiceResponse = $this->_arInvoiceHelper->createRevInvoice($inputArray);    # Creating Invoice using API CreateInvoiceRev

        if (empty($createInvoiceResponse)) {
			$logger->info("Order Id : " . $order->getIncrementId());
			$logger->info("API Response not Found.");
            //logging audit log
            $this->auditLog->saveAuditLog([
                'user_id' => "",
                'action' => 'AR Invoice Failed',
                'description' => "Fail to create AR Invoice for order with id" . $incrementId,
                'client_ip' => "",
                'module_name' => "Dyode_ArInvoice"
            ]);
			throw new \Exception("API Response not Found", 1);
        }

        /**
         * Create Invoice Response Validation
         */
        if ($createInvoiceResponse->OK != true) {   # Create Invoice Response is false
            $order->setState("processing")->setStatus("estimate_issue");    # Change the Order Status and Order State
            $order->addStatusToHistory($order->getStatus(), 'Estimate not Issued');   # Add Comment to Order History
            $order->save();     # Save the Changes in Order Status & History

            //logging audit log
            $this->auditLog->saveAuditLog([
                'user_id' => "",
                'action' => 'AR Invoice Creation',
                'description' => "Fail to create AR Invoice for order with id" . $incrementId,
                'client_ip' => "",
                'module_name' => "Dyode_ArInvoice"
            ]);
            // Logger
            $logger->info("Order Id : " . $order->getIncrementId());
            $logger->info($createInvoiceResponse->INFO);

            return true;
        } else {  # Create Invoice Response is true
            $estimateNumber = $invoiceNumber = $createInvoiceResponse->DATA->INV_NO;    # Save Estimate Number in Order
            $order->setData('estimatenumber', $estimateNumber);
            $order->addStatusToHistory($order->getStatus(), 'Estimate Number: ' . $estimateNumber );     # Add Comment to Order History
            $order->save();

            //logging audit log
            $this->auditLog->saveAuditLog([
                'user_id' => "",
                'action' => 'AR Invoice Creation',
                'description' => "Created AR Invoice (No : " . $createInvoiceResponse->DATA->INV_NO. ") Successfully for order with id" . $incrementId,
                'client_ip' => "",
                'module_name' => "Dyode_ArInvoice"
            ]);

            /**
             * Customer Status Validation
             */
            if ((!empty($customerStatus)) && ($customerStatus->customerstatus == false || $customerStatus->addressmismatch == true || $customerStatus->soft == true)) {
                $order->setState("pending_payment")->setStatus("creditreview");    # Change the Order Status and Order State
                $order->addStatusToHistory($order->getStatus(), 'Your Credit is being Reviewed');     # Add Comment to Order History
                $order->save();     # Save the Changes in Order Status & History

                # Notify Customer  - incomplete...
                # Notify Credit Department to Review  - incomplete...
            } else {
                $order->setState("processing")->setStatus("processing");    # Change the Order Status and Order State
                $order->save();     # Save the Changes in Order Status & History
                $referId = $incrementId;

                if ($downPaymentAmount !== '0') {
                    # Web Down Payment API
                    $webDownPaymentResponse = $this->_arInvoiceHelper->webDownPayment($accountNumber,
                        $downPaymentAmount, $invoiceNumber, $referId);

                    if ($webDownPaymentResponse->OK != true) {
                        //logging audit log
                        $this->auditLog->saveAuditLog([
                            'user_id' => "",
                            'action' => 'AR Web Down Payment Failure',
                            'description' => "Fail to create web down payment for order with id" . $incrementId,
                            'client_ip' => "",
                            'module_name' => "Dyode_ArInvoice"
                        ]);
                    } else {
                        //logging audit log
                        $this->auditLog->saveAuditLog([
                            'user_id' => "",
                            'action' => 'AR Web Down Payment Success',
                            'description' => " Web Down Payment Success for order with id" . $incrementId,
                            'client_ip' => "",
                            'module_name' => "Dyode_ArInvoice"
                        ]);
                    }
                }
            }

            //generating magento invoice
            if($order->canInvoice()) {
                $invoice = $this->_invoiceService->prepareInvoice($order);
                $invoice->register();
                $invoice->save();
                $transactionSave = $this->_transaction->addObject($invoice)->addObject($invoice->getOrder());
                $transactionSave->save();
            }

            return true;
        }

        return false;
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
     * @param $orderId
     *
     * @return array|\Dyode\ArInvoice\Helper\Array
     *
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
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
