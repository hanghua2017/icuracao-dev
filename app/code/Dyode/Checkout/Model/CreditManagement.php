<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       23/08/2018
 */
namespace Dyode\Checkout\Model;

use Dyode\Checkout\Model\InfoProcessor\SaveManager;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Dyode\Checkout\Api\CreditManagementInterface;
use Dyode\Checkout\Helper\CuracaoHelper;

class CreditManagement extends \Magento\Framework\Model\AbstractModel implements CreditManagementInterface
{

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var \Magento\Checkout\Api\PaymentInformationManagementInterface
     */
    protected $paymentInformationManagement;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Dyode\Checkout\Model\InfoProcessor\SaveManager
     */
    protected $manager;

    /**
     * CreditManagement constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     * @param \Dyode\Checkout\Model\InfoProcessor\SaveManager $manager
     */
    public function __construct(
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        PaymentInformationManagementInterface $paymentInformationManagement,
        CuracaoHelper $curacaoHelper,
        SaveManager $manager
    ) {
        $this->_customerSession = $customerSession;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->curacaoHelper = $curacaoHelper;
        $this->_checkoutSession = $checkoutSession;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function applyCuracaoCreditInTotals($cartId)
    {
        $downPayment = $this->collectDownPaymentFromSession();
        return $this->getPaymentInformation($cartId, $downPayment);
    }

    /**
     * {@inheritdoc}
     */
    public function removeCuracaoCreditFromTotals($cartId)
    {
        return $this->getPaymentInformation($cartId);
    }

    /**
     * Collects curacao down payment information from the checkout session.
     *
     * @return float $downPayment
     */
    public function collectDownPaymentFromSession()
    {
        /** @var \Magento\Framework\DataObject $curacaoInfo */
        $downPayment = 0.00;
        $curacaoInfo = $this->_checkoutSession->getCuracaoInfo();

        if ($curacaoInfo) {
            $downPayment = $curacaoInfo->getDownPayment();
        }

        return $downPayment;
    }

    /**
     * Prepare payment information with updated totals.
     *
     * @param string|int $cartId
     * @param float|bool $downPayment
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPaymentInformation($cartId, $downPayment = false)
    {
        if (!$this->_customerSession->isLoggedIn()) {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            $cartId = $quoteIdMask->getQuoteId();
        }

        $paymentInformation = $this->paymentInformationManagement->getPaymentInformation($cartId);
        $shippingAddressInfo = $this->curacaoHelper->getShippingCarrierInfoByQuoteItems($cartId);

        if ($downPayment === false) {
            return $this->manager->updateShippingTotal($paymentInformation, $shippingAddressInfo);
        }
        return $this->manager->updateShippingTotal($paymentInformation, $shippingAddressInfo, $downPayment);
    }
}
