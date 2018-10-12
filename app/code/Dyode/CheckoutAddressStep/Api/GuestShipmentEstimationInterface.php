<?php
/**
 * Dyode_CheckoutAddressStep Magento2 Module.
 *
 * Adding new checkout step in the one page checkout.
 *
 * @package   Dyode
 * @module    Dyode_CheckoutAddressStep
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */
namespace Dyode\CheckoutAddressStep\Api;

use Dyode\CheckoutAddressStep\Api\Data\AddressInterface;

/**
 * Interface GuestShipmentEstimationInterface
 */
interface GuestShipmentEstimationInterface
{

    /**
     * Estimate shipping by address and return list of available shipping methods
     *
     * @param mixed $cartId
     * @param AddressInterface $address
     * @return array An array of shipping methods
     */
    public function estimateByExtendedAddress($cartId, AddressInterface $address);
}