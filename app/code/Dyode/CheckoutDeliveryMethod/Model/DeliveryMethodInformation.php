<?php
namespace Dyode\CheckoutDeliveryMethod\Model;
 
use \Dyode\CheckoutDeliveryMethod\Api\Data\DeliveryMethodInformationInterface;
 
/**
 * Model that contains updated cart information.
 */
class DeliveryMethodInformation implements DeliveryMethodInformationInterface {
 
    /**
     * The quoteItemId for this quote item.
     * @var string
     */
    protected $quoteItemId;
 
    /**
     * The delivery type value for this quote item.
     * @var string
     */
    protected $delivery_type;
 
    /**
     * Gets the quoteItem Id.
     *
     * @api
     * @return string
     */
    public function getQuoteId() {
        return $this->quoteItemId;
    }
 
    /**
     * Sets the quoteItem Id.
     *
     * @api
     * @param int $quoteItemId
     */
    public function setQuoteId($quoteItemId) {
        $this->quoteItemId = $quoteItemId;
    }
 
    /**
     * Gets the quantity.
     *
     * @api
     * @return string
     */
    public function getDeliveryType() {
        return $this->delivery_type;
    }
 
    /**
     * Sets the quantity.
     *
     * @api
     * @param int $qty
     * @return void
     */
    public function setDeliveryType($delivery_type) {
        $this->delivery_type = $delivery_type;
    }
}