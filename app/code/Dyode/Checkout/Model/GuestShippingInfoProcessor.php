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
namespace Dyode\Checkout\Model;

use Dyode\Checkout\Api\GuestShippingInfoInterface;
use Dyode\Checkout\Api\Data\ShippingInformationInterface;
use Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod;

/**
 * Guest ShippingInfo Processor
 *
 * This will update the shipping carrier information against each quote item.
 * It also performs Magento's default shipping info saving action.
 */
class GuestShippingInfoProcessor implements GuestShippingInfoInterface
{
    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Magento\Checkout\Api\ShippingInformationManagementInterface
     */
    protected $shippingInformationManagement;

    /**
     * @var \Magento\Quote\Api\PaymentMethodManagementInterface
     */
    protected $paymentMethodManagement;

    /**
     * @var \Magento\Checkout\Model\PaymentDetailsFactory
     */
    protected $paymentDetailsFactory;

    /**
     * @var \Magento\Quote\Api\CartTotalRepositoryInterface
     */
    protected $cartTotalsRepository;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * GuestShippingInfoProcessor constructor.
     *
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository,
        \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->cartTotalsRepository = $cartTotalsRepository;
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function saveAddressInformation($cartId, ShippingInformationInterface $addressInformation)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $this->saveQuoteItemShippingInfo($quoteIdMask->getQuoteId(), $addressInformation);
        $paymentDetails = $this->shippingInformationManagement->saveAddressInformation(
            $quoteIdMask->getQuoteId(),
            $addressInformation
        );

        return $this->updateShippingTotal($paymentDetails, $addressInformation);
    }

    /**
     * Save quote item information information.
     *
     * @param string|integer $cartId
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function saveQuoteItemShippingInfo($cartId, ShippingInformationInterface $addressInformation)
    {
        if (!$this->quote) {
            $this->quote = $this->quoteRepository->getActive($cartId);
        }

        $shippingInfo = $addressInformation->getShippingCarrierInfo();

        foreach ($shippingInfo as $info) {
            if (!$info
                || !isset($info['shipping_type'])
                || !isset($info['quote_item_id'])
                || !isset($info['shipping_data'])
            ) {
                continue;
            }

            $shippingType = $info['shipping_type'];

            if ($shippingType != DeliveryMethod::DELIVERY_OPTION_SHIP_TO_HOME_CODE) {
                continue;
            }

            /** @var array $carrierInfo */
            $quoteItemId = (int)$info['quote_item_id'];
            $carrierInfo = $info['shipping_data'];

            $quoteItem = $this->quote->getItemById($quoteItemId);
            $quoteItem->setShippingDetails(json_encode($carrierInfo));

            if (isset($carrierInfo['amount'])) {
                $quoteItem->setShippingCost((float)$carrierInfo['amount']);
            }
        }

        $this->quote->save();
    }

    /**
     * Update totals with total shipping amount cost.
     *
     * @param $paymentDetails
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return mixed
     */
    protected function updateShippingTotal($paymentDetails, ShippingInformationInterface $addressInformation)
    {
        /** @var \Magento\Quote\Model\Cart\Totals $totals */
        $totals = $paymentDetails->getTotals();
        $shippingAmount = $this->calculateShippingAmount($addressInformation);

        $totals->setShippingAmount($totals->getShippingAmount() + $shippingAmount);
        $totals->setShippingInclTax($totals->getShippingInclTax() + $shippingAmount);
        $totals->setSubtotal($totals->getSubtotal() + $shippingAmount);
        $totals->setSubtotalInclTax($totals->getSubtotalInclTax() + $shippingAmount);
        $totals->setSubtotalWithDiscount($totals->getSubtotalWithDiscount() + $shippingAmount);
        $totals->setGrandTotal($totals->getGrandTotal() + $shippingAmount);

        return $paymentDetails;
    }

    /**
     * Calculate shipping amount based on the quote item shipping options.
     *
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return float|int $shippingAmount
     */
    protected function calculateShippingAmount(ShippingInformationInterface $addressInformation)
    {
        $shippingAmount = 0;

        foreach ($addressInformation->getShippingCarrierInfo() as $information) {
            if (!$information
                || !isset($information['shipping_type'])
                || !isset($information['quote_item_id'])
                || !isset($information['shipping_data'])
            ) {
                continue;
            }

            $shippingType = $information['shipping_type'];

            if ($shippingType != DeliveryMethod::DELIVERY_OPTION_SHIP_TO_HOME_CODE) {
                continue;
            }

            $carrierInfo = $information['shipping_data'];

            if (isset($carrierInfo['amount'])) {
                $shippingAmount += (float)$carrierInfo['amount'];
            }
        }

        return $shippingAmount;
    }
}
