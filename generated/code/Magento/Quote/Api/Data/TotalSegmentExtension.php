<?php
namespace Magento\Quote\Api\Data;

/**
 * Extension class for @see \Magento\Quote\Api\Data\TotalSegmentInterface
 */
class TotalSegmentExtension extends \Magento\Framework\Api\AbstractSimpleObject implements TotalSegmentExtensionInterface
{
    /**
     * @return string|null
     */
    public function getGiftCards()
    {
        return $this->_get('gift_cards');
    }

    /**
     * @param string $giftCards
     * @return $this
     */
    public function setGiftCards($giftCards)
    {
        $this->setData('gift_cards', $giftCards);
        return $this;
    }

    /**
     * @return \Magento\Tax\Api\Data\GrandTotalDetailsInterface[]|null
     */
    public function getTaxGrandtotalDetails()
    {
        return $this->_get('tax_grandtotal_details');
    }

    /**
     * @param \Magento\Tax\Api\Data\GrandTotalDetailsInterface[] $taxGrandtotalDetails
     * @return $this
     */
    public function setTaxGrandtotalDetails($taxGrandtotalDetails)
    {
        $this->setData('tax_grandtotal_details', $taxGrandtotalDetails);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwOrderId()
    {
        return $this->_get('gw_order_id');
    }

    /**
     * @param string $gwOrderId
     * @return $this
     */
    public function setGwOrderId($gwOrderId)
    {
        $this->setData('gw_order_id', $gwOrderId);
        return $this;
    }

    /**
     * @return mixed[]|null
     */
    public function getGwItemIds()
    {
        return $this->_get('gw_item_ids');
    }

    /**
     * @param mixed[] $gwItemIds
     * @return $this
     */
    public function setGwItemIds($gwItemIds)
    {
        $this->setData('gw_item_ids', $gwItemIds);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwAllowGiftReceipt()
    {
        return $this->_get('gw_allow_gift_receipt');
    }

    /**
     * @param string $gwAllowGiftReceipt
     * @return $this
     */
    public function setGwAllowGiftReceipt($gwAllowGiftReceipt)
    {
        $this->setData('gw_allow_gift_receipt', $gwAllowGiftReceipt);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwAddCard()
    {
        return $this->_get('gw_add_card');
    }

    /**
     * @param string $gwAddCard
     * @return $this
     */
    public function setGwAddCard($gwAddCard)
    {
        $this->setData('gw_add_card', $gwAddCard);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwPrice()
    {
        return $this->_get('gw_price');
    }

    /**
     * @param string $gwPrice
     * @return $this
     */
    public function setGwPrice($gwPrice)
    {
        $this->setData('gw_price', $gwPrice);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwBasePrice()
    {
        return $this->_get('gw_base_price');
    }

    /**
     * @param string $gwBasePrice
     * @return $this
     */
    public function setGwBasePrice($gwBasePrice)
    {
        $this->setData('gw_base_price', $gwBasePrice);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwItemsPrice()
    {
        return $this->_get('gw_items_price');
    }

    /**
     * @param string $gwItemsPrice
     * @return $this
     */
    public function setGwItemsPrice($gwItemsPrice)
    {
        $this->setData('gw_items_price', $gwItemsPrice);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwItemsBasePrice()
    {
        return $this->_get('gw_items_base_price');
    }

    /**
     * @param string $gwItemsBasePrice
     * @return $this
     */
    public function setGwItemsBasePrice($gwItemsBasePrice)
    {
        $this->setData('gw_items_base_price', $gwItemsBasePrice);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwCardPrice()
    {
        return $this->_get('gw_card_price');
    }

    /**
     * @param string $gwCardPrice
     * @return $this
     */
    public function setGwCardPrice($gwCardPrice)
    {
        $this->setData('gw_card_price', $gwCardPrice);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwCardBasePrice()
    {
        return $this->_get('gw_card_base_price');
    }

    /**
     * @param string $gwCardBasePrice
     * @return $this
     */
    public function setGwCardBasePrice($gwCardBasePrice)
    {
        $this->setData('gw_card_base_price', $gwCardBasePrice);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwBaseTaxAmount()
    {
        return $this->_get('gw_base_tax_amount');
    }

    /**
     * @param string $gwBaseTaxAmount
     * @return $this
     */
    public function setGwBaseTaxAmount($gwBaseTaxAmount)
    {
        $this->setData('gw_base_tax_amount', $gwBaseTaxAmount);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwTaxAmount()
    {
        return $this->_get('gw_tax_amount');
    }

    /**
     * @param string $gwTaxAmount
     * @return $this
     */
    public function setGwTaxAmount($gwTaxAmount)
    {
        $this->setData('gw_tax_amount', $gwTaxAmount);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwItemsBaseTaxAmount()
    {
        return $this->_get('gw_items_base_tax_amount');
    }

    /**
     * @param string $gwItemsBaseTaxAmount
     * @return $this
     */
    public function setGwItemsBaseTaxAmount($gwItemsBaseTaxAmount)
    {
        $this->setData('gw_items_base_tax_amount', $gwItemsBaseTaxAmount);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwItemsTaxAmount()
    {
        return $this->_get('gw_items_tax_amount');
    }

    /**
     * @param string $gwItemsTaxAmount
     * @return $this
     */
    public function setGwItemsTaxAmount($gwItemsTaxAmount)
    {
        $this->setData('gw_items_tax_amount', $gwItemsTaxAmount);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwCardBaseTaxAmount()
    {
        return $this->_get('gw_card_base_tax_amount');
    }

    /**
     * @param string $gwCardBaseTaxAmount
     * @return $this
     */
    public function setGwCardBaseTaxAmount($gwCardBaseTaxAmount)
    {
        $this->setData('gw_card_base_tax_amount', $gwCardBaseTaxAmount);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwCardTaxAmount()
    {
        return $this->_get('gw_card_tax_amount');
    }

    /**
     * @param string $gwCardTaxAmount
     * @return $this
     */
    public function setGwCardTaxAmount($gwCardTaxAmount)
    {
        $this->setData('gw_card_tax_amount', $gwCardTaxAmount);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwPriceInclTax()
    {
        return $this->_get('gw_price_incl_tax');
    }

    /**
     * @param string $gwPriceInclTax
     * @return $this
     */
    public function setGwPriceInclTax($gwPriceInclTax)
    {
        $this->setData('gw_price_incl_tax', $gwPriceInclTax);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwBasePriceInclTax()
    {
        return $this->_get('gw_base_price_incl_tax');
    }

    /**
     * @param string $gwBasePriceInclTax
     * @return $this
     */
    public function setGwBasePriceInclTax($gwBasePriceInclTax)
    {
        $this->setData('gw_base_price_incl_tax', $gwBasePriceInclTax);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwCardPriceInclTax()
    {
        return $this->_get('gw_card_price_incl_tax');
    }

    /**
     * @param string $gwCardPriceInclTax
     * @return $this
     */
    public function setGwCardPriceInclTax($gwCardPriceInclTax)
    {
        $this->setData('gw_card_price_incl_tax', $gwCardPriceInclTax);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwCardBasePriceInclTax()
    {
        return $this->_get('gw_card_base_price_incl_tax');
    }

    /**
     * @param string $gwCardBasePriceInclTax
     * @return $this
     */
    public function setGwCardBasePriceInclTax($gwCardBasePriceInclTax)
    {
        $this->setData('gw_card_base_price_incl_tax', $gwCardBasePriceInclTax);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwItemsPriceInclTax()
    {
        return $this->_get('gw_items_price_incl_tax');
    }

    /**
     * @param string $gwItemsPriceInclTax
     * @return $this
     */
    public function setGwItemsPriceInclTax($gwItemsPriceInclTax)
    {
        $this->setData('gw_items_price_incl_tax', $gwItemsPriceInclTax);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwItemsBasePriceInclTax()
    {
        return $this->_get('gw_items_base_price_incl_tax');
    }

    /**
     * @param string $gwItemsBasePriceInclTax
     * @return $this
     */
    public function setGwItemsBasePriceInclTax($gwItemsBasePriceInclTax)
    {
        $this->setData('gw_items_base_price_incl_tax', $gwItemsBasePriceInclTax);
        return $this;
    }
}
