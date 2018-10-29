<?php
/**
 * Dyode_CuracaoPayment Magento2 Module.
 *
 * Provide the facility: curacao custom payment method
 *
 * @package   Dyode
 * @module    Dyode_CuracaoPayment
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\CuracaoPayment\Model\Payment;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Payment\Model\Method\AbstractMethod;

class CuracaoFullPayment extends AbstractMethod
{

    protected $_code = "curacaofullpayment";

    protected $_isOffline = true;

    public function isAvailable(CartInterface $quote = null)
    {
        return parent::isAvailable($quote);
    }
}
