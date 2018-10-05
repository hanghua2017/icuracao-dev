<?php

namespace Dyode\CheckoutDeliveryMethod\Api\Data;

Interface ShippingInformationInterface
{

    const SHIPPING_CARRIER_INFO = 'shipping_carrier_info';

    public function getShippingCarrierInfo();

    public function setShippingCarrierInfo($shippingCarrierInfo);
}