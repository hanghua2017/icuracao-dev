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
namespace Dyode\CheckoutAddressStep\Model\Quote;

use Dyode\CheckoutAddressStep\Api\Data\AddressInterface;

class Address extends \Magento\Framework\DataObject implements AddressInterface
{
    /**
     * {@inheritdoc}
     */
    public function getZipCode()
    {
        return $this->getData(self::ZIP_CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setZipCode($postcode)
    {
        return $this->setData(self::ZIP_CODE, $postcode);
    }
}