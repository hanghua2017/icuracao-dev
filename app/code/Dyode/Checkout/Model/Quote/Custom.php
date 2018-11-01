<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       20/08/2018
 */

namespace Dyode\Checkout\Model\Quote;

use Magento\Quote\Api\Data\ShippingAssignmentInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Address\Total;
use Magento\Quote\Model\Quote\Address\Total\AbstractTotal;

/**
 * Class Custom
 */
class Custom extends AbstractTotal
{


    /**
     * Here basically we need to set curacao down-payment value.
     *
     * Since we want to collect the down-payment from ARWebservice and it completely depends on the grand total
     * component, we cannot calculate the value here. Even though we calculate the value by sending an API
     * request, it will be a wrong value.
     *
     * Hence we are not doing anything here. But this total should exist since this total segment is used later
     * to fill the correct curacao credit and thus which will be filtered in the frontend and properly displayed.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param \Magento\Quote\Api\Data\ShippingAssignmentInterface $shippingAssignment
     * @param \Magento\Quote\Model\Quote\Address\Total $total
     * @return $this|\Magento\Quote\Model\Quote\Address\Total\AbstractTotal
     */
    public function collect(Quote $quote, ShippingAssignmentInterface $shippingAssignment, Total $total)
    {
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
            'value' => 0,
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

}
