<?php
namespace Dyode\CheckoutDeliveryMethod\Api\Data;
 
/**
 * Interface for tracking quote item updates.
 */
interface DeliveryMethodInformationInterface {
    /**
     * Gets the quote item id.
     *
     * @api
     * @return string
     */
    public function getQuoteId();
 
    /**
     * Sets the quote item id .
     *
     * @api
     * @param int $sku
     */
    public function setQuoteId($quoteItemId);
 
    /**
     * Gets the delivery type of quote item.
     *
     * @api
     * @return string
     */
    public function getDeliveryType();
 
    /**
     * Sets the delivery type of quote items.
     *
     * @api
     * @param string $delivery_type
     * @return void
     */
    public function setDeliveryType($delivery_type);
}