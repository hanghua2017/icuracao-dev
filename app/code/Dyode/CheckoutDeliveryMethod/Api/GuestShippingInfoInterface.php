<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dyode\CheckoutDeliveryMethod\Api;

use Dyode\CheckoutDeliveryMethod\Api\Data\ShippingInformationInterface;

/**
 * Interface for managing guest shipping address information
 *
 * @api
 */
interface GuestShippingInfoInterface
{
    /**
     * @param string $cartId
     * @param \Dyode\CheckoutDeliveryMethod\Api\Data\ShippingInformationInterface $addressInformation
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function saveAddressInformation(
        $cartId,
        ShippingInformationInterface $addressInformation
    );
}
