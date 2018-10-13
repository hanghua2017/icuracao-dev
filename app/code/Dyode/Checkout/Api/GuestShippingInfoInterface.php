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
namespace Dyode\Checkout\Api;

use Dyode\Checkout\Api\Data\ShippingInformationInterface;

/**
 * Interface for managing guest shipping address information
 *
 * @api
 */
interface GuestShippingInfoInterface
{
    /**
     * @param string $cartId
     * @param \Dyode\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function saveAddressInformation(
        $cartId,
        ShippingInformationInterface $addressInformation
    );
}
