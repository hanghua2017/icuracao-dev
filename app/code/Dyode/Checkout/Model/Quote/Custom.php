<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       20/08/2018
 */

namespace Dyode\Checkout\Model\Quote;

/**
 * Class Custom
 *
 * @package Dyode\Checkout\Model\Quote
 */
class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var int
     */
    protected $_curacaocredit;

    /**
     * Custom constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
    ) {
        $this->_customerSession = $customerSession;
        $this->_priceCurrency = $priceCurrency;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|\Magento\Quote\Model\Quote\Address\Total\AbstractTotal
     */
    public function collect(
        \Magento\Quote\Model\Quote $quote,
        \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
        \Magento\Quote\Model\Quote\Address\Total $total
    ) {
        parent::collect($quote, $shippingAssignment, $total);

        $address = $shippingAssignment->getShipping()->getAddress();
        if ($address->getAddressType() != 'billing') {
            return $this;
        }
        if (!$this->_customerSession->isLoggedIn()) {
            return $this;
        }

        if ($quote->getCustomer()->getCustomAttribute('curacaocustid') != null) {
            $curaAccId = $quote->getCustomer()->getCustomAttribute('curacaocustid')->getValue();
            $downPayment = false;

            /** @var \stdClass $downPaymentSessionInfo */
            $downPaymentSessionInfo = $this->_customerSession->getDownPayment();
            if ($downPaymentSessionInfo) {
                $downPayment = $downPaymentSessionInfo->DOWNPAYMENT;
            }

            $this->_curacaocredit = 0;

            if (!$curaAccId || $downPayment === false) {
                return $this;
            }
            if ($quote->getCustomer()->getId() && $curaAccId != 0) {
                if ($quote->getUseCredit() && $downPayment > 0) {
                    //Calculate the discount
                    $subTotal = $quote->getSubtotal();
                    $discount = $subTotal - $downPayment;
                    $customDiscount = -$discount;
                    $this->_curacaocredit = -$discount;

                    $total->addTotalAmount('customdiscount', $customDiscount);
                    $total->addBaseTotalAmount('customdiscount', $customDiscount);
                    $quote->setCuracaocreditUsed($this->_curacaocredit);
                }
            }
        }
        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
    {
        return [
            'code'  => 'curacao_discount',
            'title' => $this->getLabel(),
            'value' => $this->_curacaocredit,
        ];
    }

    /**
     * get label
     *
     * @return string
     */
    public function getLabel()
    {
        return __('Curacao Credit');
    }
}
