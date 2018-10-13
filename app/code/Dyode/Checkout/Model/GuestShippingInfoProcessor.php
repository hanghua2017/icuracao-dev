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
use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Framework\Exception\NoSuchEntityException;

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
     * GuestShippingInfoProcessor constructor.
     *
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement
     * @param \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement
     * @param \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory
     * @param \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
     */
    public function __construct(
        \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory,
        \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement,
        \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement,
        \Magento\Checkout\Model\PaymentDetailsFactory $paymentDetailsFactory,
        \Magento\Quote\Api\CartTotalRepositoryInterface $cartTotalsRepository
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->paymentMethodManagement = $paymentMethodManagement;
        $this->paymentDetailsFactory = $paymentDetailsFactory;
        $this->cartTotalsRepository = $cartTotalsRepository;
    }

    /**
     * {@inheritDoc}
     */
    public function saveAddressInformation(
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        try {
            $this->saveQuoteItemShippingInfo($quoteIdMask->getQuoteId(), $addressInformation);
            $this->shippingInformationManagement->saveAddressInformation(
                $quoteIdMask->getQuoteId(),
                $addressInformation
            );
        } catch (NoSuchEntityException $exception) {
            /**
             * since the carrier_code and method_code are null, we are expecting this exception here.
             * No worries.. let's continue here.
             */
            /** @var \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails */
            $paymentDetails = $this->paymentDetailsFactory->create();
            $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($quoteIdMask->getQuoteId()));
            $paymentDetails->setTotals($this->cartTotalsRepository->get($quoteIdMask->getQuoteId()));
            $this->updateShippingTotal($paymentDetails);
            return $paymentDetails;
        }

        /** @var \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails */
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($quoteIdMask->getQuoteId()));
        $paymentDetails->setTotals($this->cartTotalsRepository->get($quoteIdMask->getQuoteId()));
        $this->updateShippingTotal($paymentDetails);
        return $paymentDetails;
    }

    /**
     * @param string|int $cartId
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     */
    public function saveQuoteItemShippingInfo($cartId, ShippingInformationInterface $addressInformation)
    {
        //here is the saving of shipping info in the quote item.
    }

    /**
     * @param \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    protected function updateShippingTotal(
        PaymentDetailsInterface $paymentDetails,
        ShippingInformationInterface $addressInformation
    ) {
        return $paymentDetails;
    }
}
