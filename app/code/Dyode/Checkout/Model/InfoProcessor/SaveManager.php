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

use Dyode\Checkout\Api\Data\ShippingInformationInterface;
use Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod;
use Magento\Quote\Api\CartRepositoryInterface;

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
     * SaveManager constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     */
    public function __construct(CartRepositoryInterface $quoteRepository)
    {
        $this->quoteRepository = $quoteRepository;
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
     * @param $paymentDetails
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @param int $curacaoDiscount
     * @return mixed
     */
    public function updateShippingTotal(
        $paymentDetails,
        ShippingInformationInterface $addressInformation,
        $curacaoDiscount = 0
    ) {
        /** @var \Magento\Quote\Model\Cart\Totals $totals */
        $totals = $paymentDetails->getTotals();
        $shippingAmount = $this->calculateShippingAmount($addressInformation);
        $grandTotal = $totals->getBaseGrandTotal() + $shippingAmount;
        $calculatedCuracaoDiscount = 0;

        if ($curacaoDiscount !== 0) {
            $calculatedCuracaoDiscount = -(float)($grandTotal - $curacaoDiscount);
            $grandTotal = $curacaoDiscount;
        }

        $totals->setShippingAmount($totals->getShippingAmount() + $shippingAmount);
        $totals->setGrandTotal($grandTotal);

        //total segment is also updating; this field is used to show grand total in the checkout
        $totalSegments = $totals->getTotalSegments();
        if ($totalSegments && $totalSegments['grand_total']) {
            $totalSegments['grand_total']->setValue($grandTotal);
        }
        if ($totalSegments && $totalSegments['curacao_discount']) {
            $totalSegments['curacao_discount']->setValue($calculatedCuracaoDiscount);
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
