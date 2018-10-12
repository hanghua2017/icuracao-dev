<?php
/**
 * Dyode_CheckoutAddressStep Magento2 Module.
 *
 * Setting the Shipping information.
 *
 * @package   Dyode
 * @module    Dyode_CheckoutAddressStep
 * @author    Kavitha <kavitha@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\CheckoutAddressStep\Model;

use Dyode\CheckoutAddressStep\Api\Data\ShippingInformationInterface;

class ShippingInformation implements ShippingInformationInterface{

    /**
     * {@inheritdoc}
     */
    public function getShippingAddress()
    {
        return $this->getData(self::SHIPPING_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingAddress(\Magento\Quote\Api\Data\AddressInterface $address)
    {
        return $this->setData(self::SHIPPING_ADDRESS, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return $this->getData(self::BILLING_ADDRESS);
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(\Magento\Quote\Api\Data\AddressInterface $address)
    {
        return $this->setData(self::BILLING_ADDRESS, $address);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingMethodCode()
    {
        return $this->getData(self::SHIPPING_METHOD_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingMethodCode($code)
    {
        return $this->setData(self::SHIPPING_METHOD_CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingCarrierCode()
    {
        return $this->getData(self::SHIPPING_CARRIER_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingCarrierCode($code)
    {
        return $this->setData(self::SHIPPING_CARRIER_CODE, $code);
    }

    /**
     * {@inheritdoc}
     */
    public function getExtensionAttributes()
    {
        return $this->_getExtensionAttributes();
    }

    /**
     * {@inheritdoc}
     */
    public function setExtensionAttributes(
        \Magento\Checkout\Api\Data\ShippingInformationExtensionInterface $extensionAttributes
    ) {
        return $this->_setExtensionAttributes($extensionAttributes);
    }

    /**
     * {@inheritdoc}
     */

    public function getShippingCarrierInfo(){
        return $this->getData(self::SHIPPING_CARRIER_INFO);
    }

     /**
     * {@inheritdoc}
     */
    public function setShippingCarrierInfo($carrierInfo)
    {
        return $this->setData(self::SHIPPING_CARRIER_INFO, $carrierInfo);
    }

}
?>