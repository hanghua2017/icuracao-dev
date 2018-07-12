<?php
namespace Magento\Sales\Api\Data;

/**
 * Extension class for @see \Magento\Sales\Api\Data\CreditmemoInterface
 */
class CreditmemoExtension extends \Magento\Framework\Api\AbstractSimpleObject implements CreditmemoExtensionInterface
{
    /**
     * @return float|null
     */
    public function getBaseCustomerBalanceAmount()
    {
        return $this->_get('base_customer_balance_amount');
    }

    /**
     * @param float $baseCustomerBalanceAmount
     * @return $this
     */
    public function setBaseCustomerBalanceAmount($baseCustomerBalanceAmount)
    {
        $this->setData('base_customer_balance_amount', $baseCustomerBalanceAmount);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getCustomerBalanceAmount()
    {
        return $this->_get('customer_balance_amount');
    }

    /**
     * @param float $customerBalanceAmount
     * @return $this
     */
    public function setCustomerBalanceAmount($customerBalanceAmount)
    {
        $this->setData('customer_balance_amount', $customerBalanceAmount);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getBaseGiftCardsAmount()
    {
        return $this->_get('base_gift_cards_amount');
    }

    /**
     * @param float $baseGiftCardsAmount
     * @return $this
     */
    public function setBaseGiftCardsAmount($baseGiftCardsAmount)
    {
        $this->setData('base_gift_cards_amount', $baseGiftCardsAmount);
        return $this;
    }

    /**
     * @return float|null
     */
    public function getGiftCardsAmount()
    {
        return $this->_get('gift_cards_amount');
    }

    /**
     * @param float $giftCardsAmount
     * @return $this
     */
    public function setGiftCardsAmount($giftCardsAmount)
    {
        $this->setData('gift_cards_amount', $giftCardsAmount);
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
}
