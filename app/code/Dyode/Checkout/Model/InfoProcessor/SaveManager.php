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

use Dyode\ARWebservice\Helper\Data as ARWebserviceHelper;
use Dyode\Checkout\Api\Data\ShippingInformationInterface;
use Dyode\Checkout\Helper\CuracaoHelper;
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
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * @var \Dyode\ARWebservice\Helper\Data
     */
    protected $arWebserviceHelper;

    /**
     * SaveManager constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     * @param \Dyode\ARWebservice\Helper\Data $arWebserviceHelper
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        CuracaoHelper $curacaoHelper,
        ARWebserviceHelper $arWebserviceHelper
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->curacaoHelper = $curacaoHelper;
        $this->arWebserviceHelper = $arWebserviceHelper;
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
     * Changing shipping amount: We have shipping amount based on the quote item instead of quote. Hence according
     * to the user selection of shipping methods against each quote item, we are calculating the totals shipping
     * amount by looping through each quote items data.
     *
     * Changing grand total: Grand total which we are getting at this stage has tax and discount applied in it. So
     * we need to add shipping cost along to that in order to make the grand total correct.
     *
     * Curacao credit: This is applicable to only curacao users. We are just showing this data as an initial
     * down payment, just after the "Order Total" part. Hence no need to change other totals based on this.
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
        $curacaoDiscount = $this->curacaoHelper->getCuracaoDownPayment();
        $grandTotal = $totals->getBaseGrandTotal() + $shippingAmount;

        if ($includeCuracaoTotal) {
            $curacaoDiscount = $this->curacaoHelper->getCuracaoDownPayment();
            $curacaoInfo = $this->curacaoHelper->getCuracaoSessionInformation();

            if ($this->curacaoHelper->hasCuracaoCreditUsed() && $curacaoInfo->getAccountNumber()) {

                //send api call to collect user info.
                $postData = [
                    'cust_id' => $curacaoInfo->getAccountNumber(),
                    'amount'  => $grandTotal,
                ];
                $verifyResult = $this->arWebserviceHelper->verifyPersonalInfm($postData);

                if ($verifyResult) {
                    $curacaoDiscount = (float)$verifyResult->DOWNPAYMENT;
                    $this->curacaoHelper->updateCuracaoSessionDetails(['down_payment' => $curacaoDiscount]);
                }
            }
        }

        $totals->setShippingAmount($shippingAmount);
        $totals->setGrandTotal($grandTotal);
        $totalSegments = $totals->getTotalSegments();

        if ($totalSegments && isset($totalSegments['grand_total'])) {
            $totalSegments['grand_total']->setValue($grandTotal);
        }

        if ($totalSegments && isset($totalSegments['shipping'])) {
            $totalSegments['shipping']->setValue($shippingAmount);
        }

        if ($totalSegments && isset($totalSegments['curacao_discount'])) {
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
