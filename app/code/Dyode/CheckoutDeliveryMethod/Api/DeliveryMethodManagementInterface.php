<?php
namespace Dyode\CheckoutDeliveryMethod\Api;
 
interface DeliveryMethodManagementInterface {
    /**
     * Updates the specified quote items delivery_type and store_location attributes.
     *
     * @api
     * @param int $cartId
     * @param \Dyode\DeliveryMethod\Api\Data\DeliveryMethodInformation[] $quoteItemsPostData
     * @return string $response
     */
    public function updateDeliveryMethodOnQuote($quoteId, $quoteItemsPostData = null);
}