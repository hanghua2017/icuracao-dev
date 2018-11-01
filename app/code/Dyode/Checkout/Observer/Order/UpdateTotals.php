<?php
/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout core module.
 *
 * @pakcage   Dyode
 * @module    Dyode_Checkout
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Checkout\Observer\Order;


use Dyode\Checkout\Helper\CuracaoHelper;
use Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod;
use Magento\Framework\Data\ObjectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Api\CartRepositoryInterface;

class UpdateTotals implements ObserverInterface
{

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * @var \Magento\Framework\Data\ObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Sales\Model\Order\Payment
     */
    protected $payment;

    /**
     * @var \Magento\Sales\Model\Order
     */
    protected $order;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var float
     */
    protected $shippingAmount;

    /**
     * @var float
     */
    protected $grandTotal;

    /**
     * UpdateTotals constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Framework\Data\ObjectFactory $dataObjectFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        ObjectFactory $dataObjectFactory,
        Json $jsonHelper,
        CuracaoHelper $curacaoHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->jsonHelper = $jsonHelper;
        $this->curacaoHelper = $curacaoHelper;
    }

    /**
     * Main entry point.
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        $this->payment = $observer->getPayment();
        $this->order = $this->payment->getOrder();

        $this->updateOrderTotals();
        $this->updateOrderItemsInformation();

        return $this;
    }

    /**
     * Updating order totals.
     *
     * We need to update grand_total, shipping_amount basically. All other totals seems good.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function updateOrderTotals()
    {
        $this->order->setBaseShippingAmount($this->shippingAmount());
        $this->order->setShippingAmount($this->shippingAmount());
        $this->order->setBaseGrandTotal($this->grandTotal());
        $this->order->setGrandTotal($this->grandTotal());
        $this->updateTotalDue();

        return $this;
    }

    /**
     * Update shipping information and pickup store information corresponds to the order items.
     *
     * @return $this
     */
    public function updateOrderItemsInformation()
    {
        foreach ($this->order->getAllVisibleItems() as $orderItem) {

            /** @var \Magento\Sales\Api\Data\OrderItemInterface $orderItem */
            $quoteItem = $this->quote->getItemById($orderItem->getQuoteItemId());

            if ($orderItem->getIsVirtual()) {

                //for warranty products
                if ($quoteItem->getWarrantyParentItemId()) {
                    $orderItem->setWarrantyParentItemId($quoteItem->getWarrantyParentItemId());
                }
            }

            $orderItem->setDeliveryType($quoteItem->getDeliveryType());
            $orderItem->setPickupLocation($quoteItem->getPickupLocation());
            $orderItem->setShippingDetails($quoteItem->getShippingDetails());
        }

        return $this;
    }

    /**
     * Updating total_due of order.
     *
     * This is required because, this is the amount which is passed to the payment method opted.
     * In the case of curacao credit user, we should use the credit amount as the payment amount.
     *
     * @return $this
     */
    public function updateTotalDue()
    {
        if ($this->curacaoHelper->hasCuracaoCreditUsed()) {
            $curacaoCredit = $this->curacaoHelper->getCuracaoDownPayment();
            $this->order->setIsCuracaoCreditUsed(true);
            $this->order->setCuracaoDownPayment((float)$curacaoCredit);

            //if downPayment = 0, paymentMethod  = curacaoFullPayment; No need to update totalDue in that case.
            if (!$curacaoCredit) {
                return $this;
            }

            $grandTotal = $this->order->getGrandTotal();
            $totalPaid = $grandTotal - (float)$curacaoCredit;

            //TotalDue = grandTotal - totalPaid; We are setting totalPaid in order to make totalDue = curacaoCredit
            $this->order->setTotalPaid($totalPaid);
            $this->order->setBaseTotalPaid($totalPaid);
        }

        return $this;
    }

    /**
     * Provide new grand total.
     *
     * Grand Total = Current Grand Total + Shipping Amount Collected Against Order Items
     *
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function grandTotal()
    {
        if (!$this->grandTotal) {
            $this->grandTotal = $this->order->getBaseGrandTotal() + $this->shippingAmount();
        }

        return $this->grandTotal;
    }

    /**
     * Provide new shipping amount.
     *
     * @return float|int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function shippingAmount()
    {
        if (!$this->shippingAmount) {
            $this->shippingAmount = $this->calculateShippingAmount();
        }

        return $this->shippingAmount;
    }

    /**
     * Quote against the order.
     *
     * @return \Magento\Quote\Api\Data\CartInterface|\Magento\Quote\Model\Quote
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function quote()
    {
        if (!$this->quote) {
            $this->quote = $this->quoteRepository->getActive($this->order->getQuoteId());
        }

        return $this->quote;
    }

    /**
     * Calculate shipping amount.
     *
     * This is calculated from the shipping_details stored against each quote_item.
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function calculateShippingAmount()
    {
        $shippingAmount = 0;

        //calculate total shipping amount by looping through the quote items.
        foreach ($this->quote()->getItems() as $quoteItem) {
            if ($quoteItem->getProductType() === 'virtual'
                || $quoteItem->getDeliveryType() != DeliveryMethod::DELIVERY_OPTION_SHIP_TO_HOME_ID
            ) {
                continue;
            }

            $shipmentData = $this->jsonHelper->unserialize($quoteItem->getShippingDetails());

            if (isset($shipmentData['amount'])) {
                $shippingAmount += $shipmentData['amount'];
            }
        }

        return $shippingAmount;
    }
}
