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
     * @var \Dyode\Checkout\Model\InfoProcessor\SaveManager
     */
    protected $manager;

    /**
     * CreditManagement constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Checkout\Api\PaymentInformationManagementInterface $paymentInformationManagement
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     * @param \Dyode\Checkout\Model\InfoProcessor\SaveManager $manager
     */
    public function __construct(
        CustomerSession $customerSession,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        PaymentInformationManagementInterface $paymentInformationManagement,
        CuracaoHelper $curacaoHelper,
        SaveManager $manager
    ) {
        $this->_customerSession = $customerSession;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->paymentInformationManagement = $paymentInformationManagement;
        $this->curacaoHelper = $curacaoHelper;
        $this->manager = $manager;
    }

    /**
     * {@inheritdoc}
     */
    public function applyCuracaoCreditInTotals($cartId)
    {
        $applyCuracaoDownPayment = true;
        return $this->getPaymentInformation($cartId, $applyCuracaoDownPayment);
    }

    /**
     * {@inheritdoc}
     */
    public function removeCuracaoCreditFromTotals($cartId)
    {
        return $this->getPaymentInformation($cartId);
    }

    /**
     * Prepare payment information with updated totals.
     * @param string $cartId
     * @param bool $downPaymentStatus
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPaymentInformation($cartId, $downPaymentStatus = false)
    {
        if (!$this->_customerSession->isLoggedIn()) {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            $cartId = $quoteIdMask->getQuoteId();
        }

        $paymentInformation = $this->paymentInformationManagement->getPaymentInformation($cartId);
        $shippingAddressInfo = $this->curacaoHelper->getShippingCarrierInfoByQuoteItems($cartId);

        return $this->manager->updateShippingTotal($paymentInformation, $shippingAddressInfo, $downPaymentStatus);
    }
}
