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
use Magento\Checkout\Api\GuestPaymentInformationManagementInterface;
use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Dyode\Checkout\Model\InfoProcessor\SaveManager;
use Dyode\Checkout\Api\Data\ShippingInformationInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * GuestPaymentInformationManagementPlugin
 *
 * This plugin is for modifying the total when discount is applied through the summary section.
 */
class GuestPaymentInformationManagementPlugin
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
     * @var \Magento\Quote\Model\QuoteIdMask
     */
    protected $quoteMask;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * GuestPaymentInformationManagementPlugin constructor.
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
     * @param \Magento\Checkout\Api\GuestPaymentInformationManagementInterface $subject
     * @param callable $proceed
     * @param string $cartId
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function aroundGetPaymentInformation(
        GuestPaymentInformationManagementInterface $subject,
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
        $this->quoteMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        return $this;
    }

    /**
     * Modifying payment totals with the quote-item based shipping amount.
     *
     * @param \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentInformation
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function afterGetPaymentInformation(PaymentDetailsInterface $paymentInformation)
    {
        $includeCuracaoTotal = true;
        $shippingAddressInfo = $this->curacaoHelper->getShippingCarrierInfoByQuoteItems($this->quoteMask->getQuoteId());
        return $this->manager->updateShippingTotal($paymentInformation, $shippingAddressInfo, $includeCuracaoTotal);
    }
}
