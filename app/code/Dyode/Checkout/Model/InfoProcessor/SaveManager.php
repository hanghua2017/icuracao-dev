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
namespace Dyode\Checkout\Model\InfoProcessor;

use Dyode\Checkout\Helper\CuracaoHelper;
use Magento\Quote\Api\CartRepositoryInterface;
use Dyode\Checkout\Api\Data\ShippingInformationInterface;
use Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod;

/**
 * SaveManager
 *
 * Intend to manage shipping saving process. This deals with those common functionalities which comes in common
 * in both guest and customer logged in scenarios.
 *
 * @package Dyode\Checkout\Model\InfoProcessor
 */
class SaveManager
{
    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * SaveManager constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     */
    public function __construct(CartRepositoryInterface $quoteRepository, CuracaoHelper $curacaoHelper)
    {
        $this->quoteRepository = $quoteRepository;
        $this->curacaoHelper = $curacaoHelper;
    }

    /**
     * Save quote item information information.
     *
     * @param $cartId
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @throws \Exception
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
     * @param \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @param bool $includeCuracaoTotal
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function updateShippingTotal(
        $paymentDetails,
        ShippingInformationInterface $addressInformation,
        $includeCuracaoTotal = false
    ) {
        /** @var \Magento\Quote\Model\Cart\Totals $totals */
        $totals = $paymentDetails->getTotals();
        $shippingAmount = $this->calculateShippingAmount($addressInformation);
        $curacaoDiscount = 0;
        $grandTotal = $totals->getBaseGrandTotal() + $shippingAmount;

        if ($includeCuracaoTotal) {
            $curacaoDiscount = $this->curacaoHelper->getCuracaoDownPayment();
        }

        $totals->setShippingAmount($totals->getShippingAmount() + $shippingAmount);
        $totals->setGrandTotal($grandTotal);

        //total segment is also updating; this field is used to show grand total in the checkout
        $totalSegments = $totals->getTotalSegments();
        if ($totalSegments && $totalSegments['grand_total']) {
            $totalSegments['grand_total']->setValue($grandTotal);
        }
        if ($totalSegments && $totalSegments['curacao_discount']) {
            $totalSegments['curacao_discount']->setValue($curacaoDiscount);
        }

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
