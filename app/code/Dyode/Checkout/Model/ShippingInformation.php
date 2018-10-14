<?php
/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout core module.
 *
 * @pakcage   Dyode
 * @module    Dyode_Checkout
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */
namespace Dyode\Checkout\Model;

use Magento\Checkout\Model\ShippingInformation as CheckoutShippingInformation;
use Dyode\Checkout\Api\Data\ShippingInformationInterface;

class ShippingInformation extends CheckoutShippingInformation implements ShippingInformationInterface
{

    /**
     * {@inheritdoc}
     */
    public function getShippingCarrierInfo()
    {
        return $this->getData(self::SHIPPING_CARRIER_INFO);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingCarrierInfo($shippingCarrierInfo)
    {
        return $this->setData(self::SHIPPING_CARRIER_INFO, $shippingCarrierInfo);
    }

}
