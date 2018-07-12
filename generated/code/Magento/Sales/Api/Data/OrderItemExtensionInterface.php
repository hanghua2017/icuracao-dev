<?php
namespace Magento\Sales\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Sales\Api\Data\OrderItemInterface
 */
interface OrderItemExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return \Magento\GiftMessage\Api\Data\MessageInterface|null
     */
    public function getGiftMessage();

    /**
     * @param \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage
     * @return $this
     */
    public function setGiftMessage(\Magento\GiftMessage\Api\Data\MessageInterface $giftMessage);

    /**
     * @return string|null
     */
    public function getGwId();

    /**
     * @param string $gwId
     * @return $this
     */
    public function setGwId($gwId);

    /**
     * @return string|null
     */
    public function getGwBasePrice();

    /**
     * @param string $gwBasePrice
     * @return $this
     */
    public function setGwBasePrice($gwBasePrice);

    /**
     * @return string|null
     */
    public function getGwPrice();

    /**
     * @param string $gwPrice
     * @return $this
     */
    public function setGwPrice($gwPrice);

    /**
     * @return string|null
     */
    public function getGwBaseTaxAmount();

    /**
     * @param string $gwBaseTaxAmount
     * @return $this
     */
    public function setGwBaseTaxAmount($gwBaseTaxAmount);

    /**
     * @return string|null
     */
    public function getGwTaxAmount();

    /**
     * @param string $gwTaxAmount
     * @return $this
     */
    public function setGwTaxAmount($gwTaxAmount);

    /**
     * @return string|null
     */
    public function getGwBasePriceInvoiced();

    /**
     * @param string $gwBasePriceInvoiced
     * @return $this
     */
    public function setGwBasePriceInvoiced($gwBasePriceInvoiced);

    /**
     * @return string|null
     */
    public function getGwPriceInvoiced();

    /**
     * @param string $gwPriceInvoiced
     * @return $this
     */
    public function setGwPriceInvoiced($gwPriceInvoiced);

    /**
     * @return string|null
     */
    public function getGwBaseTaxAmountInvoiced();

    /**
     * @param string $gwBaseTaxAmountInvoiced
     * @return $this
     */
    public function setGwBaseTaxAmountInvoiced($gwBaseTaxAmountInvoiced);

    /**
     * @return string|null
     */
    public function getGwTaxAmountInvoiced();

    /**
     * @param string $gwTaxAmountInvoiced
     * @return $this
     */
    public function setGwTaxAmountInvoiced($gwTaxAmountInvoiced);

    /**
     * @return string|null
     */
    public function getGwBasePriceRefunded();

    /**
     * @param string $gwBasePriceRefunded
     * @return $this
     */
    public function setGwBasePriceRefunded($gwBasePriceRefunded);

    /**
     * @return string|null
     */
    public function getGwPriceRefunded();

    /**
     * @param string $gwPriceRefunded
     * @return $this
     */
    public function setGwPriceRefunded($gwPriceRefunded);

    /**
     * @return string|null
     */
    public function getGwBaseTaxAmountRefunded();

    /**
     * @param string $gwBaseTaxAmountRefunded
     * @return $this
     */
    public function setGwBaseTaxAmountRefunded($gwBaseTaxAmountRefunded);

    /**
     * @return string|null
     */
    public function getGwTaxAmountRefunded();

    /**
     * @param string $gwTaxAmountRefunded
     * @return $this
     */
    public function setGwTaxAmountRefunded($gwTaxAmountRefunded);
}
