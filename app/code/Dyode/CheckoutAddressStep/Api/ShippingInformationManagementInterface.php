<?php
/**
 * Dyode_CheckoutAddressStep
 */
namespace Dyode\CheckoutAddressStep\Api;

/**
 * Interface for managing customer shipping address information
 * @api
 */
interface ShippingInformationManagementInterface
{
    /**
     * @param int $cartId
     * @param \Dyode\CheckoutAddressStep\Api\Data\ShippingInformationInterface $addressInformation
     * @return \Dyode\CheckoutAddressStep\Api\Data\PaymentDetailsInterface
     */
    public function saveAddressInformation(
        $cartId,
        \Dyode\CheckoutAddressStep\Api\Data\ShippingInformationInterface $addressInformation
    );
}
