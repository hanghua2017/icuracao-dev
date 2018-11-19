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
namespace Dyode\Checkout\Plugin\Model;

use Dyode\Checkout\Helper\CuracaoHelper;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Dyode\Checkout\Model\InfoProcessor\SaveManager;
use Dyode\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * PaymentInformationManagementPlugin
 *
 * This plugin is for modifying the total when discount is applied through the summary section.
 */
class PaymentInformationManagementPlugin
{

    /**
     * @var \Dyode\Checkout\Model\InfoProcessor\SaveManager
     */
    protected $manager;

    /**
     * @var \Dyode\Checkout\Api\Data\ShippingInformationInterface
     */
    protected $shippingInfo;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var integer
     */
    protected $quoteId;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * PaymentInformationManagementPlugin constructor.
     *
     * @param \Dyode\Checkout\Model\InfoProcessor\SaveManager $manager
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $shippingInformation
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     */
    public function __construct(
        SaveManager $manager,
        ShippingInformationInterface $shippingInformation,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CartRepositoryInterface $quoteRepository,
        CuracaoHelper $curacaoHelper
    ) {
        $this->manager = $manager;
        $this->shippingInfo = $shippingInformation;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->quoteRepository = $quoteRepository;
        $this->curacaoHelper = $curacaoHelper;
    }

    /**
     * Update payment total with quote item based shipping amount.
     *
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $subject
     * @param callable $proceed
     * @param $cartId
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     * @throws \Dyode\ARWebservice\Exception\ArResponseException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetPaymentInformation(
        PaymentInformationManagementInterface $subject,
        callable $proceed,
        $cartId
    ) {
        $this->beforeGetPaymentInformation($cartId);
        $paymentInformation = $proceed($cartId);

        return $this->afterGetPaymentInformation($paymentInformation);
    }

    /**
     * Storing cart id parameter for future purpose.
     *
     * Before the method is the most suitable hook point for this.
     *
     * @param $cartId
     * @return $this
     */
    protected function beforeGetPaymentInformation($cartId)
    {
        $this->quoteId = $cartId;

        return $this;
    }

    /**
     * Modifying payment totals with the quote-item based shipping amount.
     *
     * @param \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentInformation
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     * @throws \Dyode\ARWebservice\Exception\ArResponseException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function afterGetPaymentInformation(PaymentDetailsInterface $paymentInformation)
    {
        $includeCuracaoTotal = true;
        $shippingAddressInfo = $this->curacaoHelper->getShippingCarrierInfoByQuoteItems($this->quoteId);

        return $this->manager->updateShippingTotal($paymentInformation, $shippingAddressInfo, $includeCuracaoTotal);
    }
}
