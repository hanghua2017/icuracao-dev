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
namespace Dyode\Checkout\Api\Data;

use Magento\Checkout\Api\Data\ShippingInformationInterface as CheckoutShippingInfoInterface;

Interface ShippingInformationInterface extends CheckoutShippingInfoInterface
{

    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const SHIPPING_CARRIER_INFO = 'shipping_carrier_info';

    /**
     * Returns billing address
     *
     * @return mixed
     */
    public function getShippingCarrierInfo();

    /**
     * Set billing address if additional synchronization needed
     *
     * @param [] $shippingCarrierInfo
     * @return $this
     */
    public function setShippingCarrierInfo($shippingCarrierInfo);
}
