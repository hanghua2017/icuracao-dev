<?php


namespace Dyode\CuracaoCredit\Model\Payment;

class CuracaoPayment extends \Magento\Payment\Model\Method\AbstractMethod
{

    protected $_code = "curacaopayment";
    protected $_isOffline = true;

    public function isAvailable(
        \Magento\Quote\Api\Data\CartInterface $quote = null
    ) {
         return parent::isAvailable($quote);
    }
}
