<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       20/08/2018
 */

namespace Dyode\Checkout\Model\Quote;
/**
* Class Custom
* @package Dyode\Checkout\Model\Quote
*/
class Custom extends \Magento\Quote\Model\Quote\Address\Total\AbstractTotal
{
  /**
  * @var \Magento\Framework\Pricing\PriceCurrencyInterface
  */
  protected $_priceCurrency;
  protected $_customerSession;
  protected $_curacaocredit;
  /**
   * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency [description]
   */
  public function __construct(
      \Magento\Customer\Model\Session $customerSession,
      \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
  ) {
      $this->_customerSession = $customerSession;
      $this->_priceCurrency = $priceCurrency;
  }

  public function collect(
      \Magento\Quote\Model\Quote $quote,
      \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
      \Magento\Quote\Model\Quote\Address\Total $total
  ) {
      parent::collect($quote, $shippingAssignment, $total);
      $address = $shippingAssignment->getShipping()->getAddress();
      $customDiscount = $curaAccId = $downPayment = $discount = 0;

       if($address->getAddressType() != 'billing'){
           return $this;
       }
        if(!$this->_customerSession->isLoggedIn()){
          return $this;
        }
        $curaAccId = $quote->getCustomer()->getCustomAttribute('curacaocustid')->getValue()? $quote->getCustomer()->getCustomAttribute('curacaocustid')->getValue() : 0;
        $downPayment = $this->_customerSession->getDownPayment()? $this->_customerSession->getDownPayment() : 0;
        $this->_curacaocredit = 0;

        if($curaAccId == 0){
          return $this;
        }

        $om =   \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $om->get("Psr\Log\LoggerInterface");
        $logger->info("inside ");

        if ($quote->getCustomer()->getId() && $curaAccId != 0) {
          $logger->info("inside if ");
          if ($quote->getUseCredit() && $downPayment>0) {
            //Calculate the discount
            $subTotal  = $quote->getSubtotal();
            $discount = $subTotal - $downPayment;
            $customDiscount = -$discount;
            $this->_curacaocredit = -$discount;

            $logger->info("customer Id = ".$curaAccId. "basetotal". $quote->getSubtotal()."downpayment = ".$downPayment);
            $total->addTotalAmount('customdiscount', $customDiscount);
            $total->addBaseTotalAmount('customdiscount', $customDiscount);
            $quote->setCuracaocreditUsed($this->_curacaocredit);
          }
        }


      return $this;
   }

  /**
   * Assign subtotal amount and label to address object
   *
   * @param \Magento\Quote\Model\Quote $quote
   * @param Address\Total $total
   * @return array
   * @SuppressWarnings(PHPMD.UnusedFormalParameter)
   */
  public function fetch(\Magento\Quote\Model\Quote $quote, \Magento\Quote\Model\Quote\Address\Total $total)
  {
      return [
          'code'  => 'Custom_Discount',
          'title' => $this->getLabel(),
          'value' => $this->_curacaocredit
      ];
  }

  /**
   * get label
   * @return string
   */
  public function getLabel()
  {
      return __('Curacao Credit');
  }
}
