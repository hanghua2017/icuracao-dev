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
namespace Dyode\CheckoutAddressStep\Api\Data;

interface AddressInterface
{

    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const ZIP_CODE = 'zip_code';

    /**
     * Get postcode
     *
     * @return string
     */
    public function getZipCode();

    /**
     * Set postcode
     *
     * @param string $zipCode
     * @return $this
     */
    public function setZipCode($zipCode);
}