<?php
namespace Magento\Sales\Api\Data;

/**
 * Extension class for @see \Magento\Sales\Api\Data\OrderItemInterface
 */
class OrderItemExtension extends \Magento\Framework\Api\AbstractSimpleObject implements OrderItemExtensionInterface
{
    /**
     * @return \Magento\GiftMessage\Api\Data\MessageInterface|null
     */
    public function getGiftMessage()
    {
        return $this->_get('gift_message');
    }

    /**
     * @param \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage
     * @return $this
     */
    public function setGiftMessage(\Magento\GiftMessage\Api\Data\MessageInterface $giftMessage)
    {
        $this->setData('gift_message', $giftMessage);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwId()
    {
        return $this->_get('gw_id');
    }

    /**
     * @param string $gwId
     * @return $this
     */
    public function setGwId($gwId)
    {
        $this->setData('gw_id', $gwId);
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
    public function getGwBasePriceInvoiced()
    {
        return $this->_get('gw_base_price_invoiced');
    }

    /**
     * @param string $gwBasePriceInvoiced
     * @return $this
     */
    public function setGwBasePriceInvoiced($gwBasePriceInvoiced)
    {
        $this->setData('gw_base_price_invoiced', $gwBasePriceInvoiced);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwPriceInvoiced()
    {
        return $this->_get('gw_price_invoiced');
    }

    /**
     * @param string $gwPriceInvoiced
     * @return $this
     */
    public function setGwPriceInvoiced($gwPriceInvoiced)
    {
        $this->setData('gw_price_invoiced', $gwPriceInvoiced);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwBaseTaxAmountInvoiced()
    {
        return $this->_get('gw_base_tax_amount_invoiced');
    }

    /**
     * @param string $gwBaseTaxAmountInvoiced
     * @return $this
     */
    public function setGwBaseTaxAmountInvoiced($gwBaseTaxAmountInvoiced)
    {
        $this->setData('gw_base_tax_amount_invoiced', $gwBaseTaxAmountInvoiced);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwTaxAmountInvoiced()
    {
        return $this->_get('gw_tax_amount_invoiced');
    }

    /**
     * @param string $gwTaxAmountInvoiced
     * @return $this
     */
    public function setGwTaxAmountInvoiced($gwTaxAmountInvoiced)
    {
        $this->setData('gw_tax_amount_invoiced', $gwTaxAmountInvoiced);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwBasePriceRefunded()
    {
        return $this->_get('gw_base_price_refunded');
    }

    /**
     * @param string $gwBasePriceRefunded
     * @return $this
     */
    public function setGwBasePriceRefunded($gwBasePriceRefunded)
    {
        $this->setData('gw_base_price_refunded', $gwBasePriceRefunded);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwPriceRefunded()
    {
        return $this->_get('gw_price_refunded');
    }

    /**
     * @param string $gwPriceRefunded
     * @return $this
     */
    public function setGwPriceRefunded($gwPriceRefunded)
    {
        $this->setData('gw_price_refunded', $gwPriceRefunded);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwBaseTaxAmountRefunded()
    {
        return $this->_get('gw_base_tax_amount_refunded');
    }

    /**
     * @param string $gwBaseTaxAmountRefunded
     * @return $this
     */
    public function setGwBaseTaxAmountRefunded($gwBaseTaxAmountRefunded)
    {
        $this->setData('gw_base_tax_amount_refunded', $gwBaseTaxAmountRefunded);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getGwTaxAmountRefunded()
    {
        return $this->_get('gw_tax_amount_refunded');
    }

    /**
     * @param string $gwTaxAmountRefunded
     * @return $this
     */
    public function setGwTaxAmountRefunded($gwTaxAmountRefunded)
    {
        $this->setData('gw_tax_amount_refunded', $gwTaxAmountRefunded);
        return $this;
    }
}
