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
namespace Dyode\Checkout\Helper;

use Dyode\Checkout\Api\Data\ShippingInformationInterface;
use Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\DataObject;
use Magento\Quote\Api\CartRepositoryInterface;

class CuracaoHelper
{

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Dyode\Checkout\Api\Data\ShippingInformationInterface
     */
    protected $shippingInfo;

    /**
     * CuracaoHelper constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $shippingInformation
     */
    public function __construct(
        CheckoutSession $checkoutSession,
        CartRepositoryInterface $quoteRepository,
        ShippingInformationInterface $shippingInformation
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->quoteRepository = $quoteRepository;
        $this->shippingInfo = $shippingInformation;
    }

    /**
     * Set/Update curacao information in the checkout session
     *
     * @param array|\Magento\Framework\DataObject $curacaoInfo
     * @return $this
     */
    public function updateCuracaoSessionDetails($curacaoInfo)
    {
        $curacaoSessionInfo = $this->checkoutSession->getCuracaoInfo();

        if ($curacaoInfo instanceof DataObject) {
            $curacaoInfo = $curacaoInfo->toArray();
        }

        if (!is_array($curacaoInfo)) {
            return $this;
        }

        if ($curacaoSessionInfo) {
            $existingDetails = $curacaoSessionInfo->getData();
            $newDetails = array_merge($existingDetails, $curacaoInfo);
            $this->checkoutSession->setCuracaoInfo(new DataObject($newDetails));
        } else {
            $this->checkoutSession->setCuracaoInfo(new DataObject($curacaoInfo));
        }

        return $this;
    }

    /**
     * Prepare shipping carrier information from the quote items.
     *
     * @param string|int $quoteId
     * @param bool $asAddress
     * @return array|\Dyode\Checkout\Api\Data\ShippingInformationInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getShippingCarrierInfoByQuoteItems($quoteId, $asAddress = true)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $carrierInfo = [];
        $deliveryMethodIdCodeRelation = DeliveryMethod::deliveryIdCodeRelations();

        if ($quote) {
            foreach ($quote->getItems() as $item) {
                $deliveryType = 0;
                if (isset($deliveryMethodIdCodeRelation[$item->getDeliveryType()])) {
                    $deliveryType = $deliveryMethodIdCodeRelation[$item->getDeliveryType()];
                }

                $carrierInfo[] = [
                    'quote_item_id' => $item->getItemId(),
                    'shipping_type' => $deliveryType,
                    'shipping_data' => (array)json_decode($item->getShippingDetails(), true),
                ];
            }
        }

        if ($asAddress) {
            return $this->shippingInfo->setShippingCarrierInfo($carrierInfo);
        }
        return $carrierInfo;
    }

    /**
     * Provide curacao down payment value only if curacao user is linked.
     *
     * @return float|int $curacaoDownPayment
     */
    public function getCuracaoDownPayment()
    {
        $curacaoDownPayment = 0;
        $curacaoInfo = $this->checkoutSession->getCuracaoInfo();

        if ($curacaoInfo && $curacaoInfo->getIsUserLinked()) {
            $curacaoDownPayment = (float)$curacaoInfo->getDownPayment();
        }

        return $curacaoDownPayment;
    }
}
