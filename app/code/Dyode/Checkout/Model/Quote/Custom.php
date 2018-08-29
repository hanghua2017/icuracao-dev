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

  /**
   * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency [description]
   */
  public function __construct(
      \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
  ) {
      $this->_priceCurrency = $priceCurrency;
  }

  public function collect(
      \Magento\Quote\Model\Quote $quote,
      \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment,
      \Magento\Quote\Model\Quote\Address\Total $total
  ) {
      parent::collect($quote, $shippingAssignment, $total);
      $address = $shippingAssignment->getShipping()->getAddress();
       if($address->getAddressType() != 'billing'){
           return $this;
       }
        $customDiscount =0;
        if ($quote->getCustomer()->getId()) {
          if ($quote->getUseCredit()) {
            $customDiscount = -10;
          }else{
            $customDiscount = 10;
          }
        }

        $total->addTotalAmount('customdiscount', $customDiscount);
        $total->addBaseTotalAmount('customdiscount', $customDiscount);
        $quote->setCustomDiscount($customDiscount);

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
          'code' => 'Custom_Discount',
          'title' => $this->getLabel(),
          'value' => 10
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
