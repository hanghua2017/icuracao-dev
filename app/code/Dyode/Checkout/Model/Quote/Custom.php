<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       20/08/2018
 */

namespace Dyode\Checkout\Model\Quote;

use Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Dyode\ARWebservice\Helper\Data as ARWebserviceHelper;
use Dyode\Checkout\Helper\CuracaoHelper;

/**
 * Class Custom
 */
class Custom extends AbstractTotal
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
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * @var \Dyode\ARWebservice\Helper\Data
     */
    protected $arWebserviceHelper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;

    /**
     * @var string
     */
    protected $curacaoIdAttribute = 'curacaocustid';

    /**
     * Custom constructor.
     *
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     * @param \Dyode\ARWebservice\Helper\Data $arWebserviceHelper
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     */
    public function __construct(
        CustomerSession $customerSession,
        PriceCurrencyInterface $priceCurrency,
        CheckoutSession $checkoutSession,
        CuracaoHelper $curacaoHelper,
        ARWebserviceHelper $arWebserviceHelper,
        Json $jsonHelper
    ) {
        $this->_customerSession = $customerSession;
        $this->_priceCurrency = $priceCurrency;
        $this->checkoutSession = $checkoutSession;
        $this->curacaoHelper = $curacaoHelper;
        $this->arWebserviceHelper = $arWebserviceHelper;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|\Magento\Quote\Model\Quote\Address\Total\AbstractTotal
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
        parent::collect($quote, $shippingAssignment, $total);

        //$address = $shippingAssignment->getShipping()->getAddress();
        $curacaoDiscount = 0;

//        if ($address->getAddressType() != 'billing') {
//            return $this;
//        }

        if ($this->_customerSession->isLoggedIn()) {
            $this->collectCuracaoDiscountByCustomer($quote);
        }

//        $this->_curacaocredit = -$curacaoDiscount;
//        $total->addTotalAmount('curacao_discount', -$curacaoDiscount);
//        $total->addBaseTotalAmount('curacao_discount', -$curacaoDiscount);

        return $this;
    }

    /**
     * Assign subtotal amount and label to address object
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return array
     */
    public function fetch(Quote $quote, Total $total)
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
        return __('Initial Payment');
    }

    /**
     * Finding curacao down payment amount for the customer.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return float|int $curacaoDiscount
     */
    protected function collectCuracaoDiscountByCustomer(Quote $quote)
    {
        $curacaoDiscount = 0;
        $curacaoIdAttributeInfo = $quote->getCustomer()->getCustomAttribute($this->curacaoIdAttribute);

        if (!$curacaoIdAttributeInfo || !$curacaoIdAttributeInfo->getValue()) {
            return $curacaoDiscount;
        }

        //send api call to collect user info.
        $postData = [
            'cust_id' => $curacaoIdAttributeInfo->getValue(),
            'amount'  => $this->prepareCuracaoDiscount($quote),
        ];
        $verifyResult = $this->arWebserviceHelper->verifyPersonalInfm($postData);

        if ($verifyResult) {
            $curacaoDiscount = (float)$verifyResult->DOWNPAYMENT;
            $this->curacaoHelper->updateCuracaoSessionDetails(['down_payment' => $curacaoDiscount]);
        }

        return $curacaoDiscount;
    }

    /**
     * Collecting curacao credit down payment from the checkout session.
     *
     * @return int $curacaoDiscount
     */
    protected function collectCuracaoDiscountBySession()
    {
        $curacaoDiscount = 0;
        $curacaoInfo = $this->checkoutSession->getCuracaoInfo();

        if ($curacaoInfo) {
            $curacaoDiscount = $curacaoInfo->getDownPayment();
        }

        return $curacaoDiscount;
    }

    /**
     * Preparing curacao amount which needs to be passed to the ARWebservice.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @return float|int
     */
    protected function prepareCuracaoDiscount(Quote $quote)
    {

        $shippingAmount = 0;

        //calculate total shipping amount by looping through the quote items.
        foreach ($quote->getItems() as $quoteItem) {
            if ($quoteItem->getIsVirtual()
                || $quoteItem->getDeliveryType() != DeliveryMethod::DELIVERY_OPTION_SHIP_TO_HOME_ID
            ) {
                continue;
            }

            $shipmentData = $this->jsonHelper->unserialize($quoteItem->getShippingDetails());

            if (isset($shipmentData['amount'])) {
                $shippingAmount += $shipmentData['amount'];
            }
        }

        return $this->checkoutSession->getQuote()->getBaseGrandTotal() + $shippingAmount;
    }
}
