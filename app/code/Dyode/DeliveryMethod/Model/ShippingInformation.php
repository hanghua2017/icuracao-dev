<?php

namespace Dyode\DeliveryMethod\Model;

use Magento\Checkout\Model\ShippingInformation as CheckoutShippingInformation;
use Dyode\DeliveryMethod\Api\Data\ShippingInformationInterface;

class ShippingInformation extends CheckoutShippingInformation 
    implements ShippingInformationInterface
{

    public function getShippingCarrierInfo()
    {
        return $this->getData(self::SHIPPING_CARRIER_INFO);
    }

    public function setShippingCarrierInfo($shippingCarrierInfo)
    {
        return $this->setData(self::SHIPPING_CARRIER_INFO, $shippingCarrierInfo);
    }

}