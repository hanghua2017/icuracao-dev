<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Dyode\DeliveryMethod\Api;

/**
 * Interface for managing guest shipping address information
 * @api
 */
interface GuestShippingInfoInterface
{
    /**
     * @param string $cartId
     * @param \Magento\Checkout\Api\Data\ShippingInformationInterface $addressInformation
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function saveAddressInformation(
        $cartId,
        \Dyode\DeliveryMethod\Api\Data\ShippingInformationInterface $addressInformation
    );
}
