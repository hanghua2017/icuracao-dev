<?php
namespace Magento\Quote\Api\Data;

/**
 * ExtensionInterface class for @see \Magento\Quote\Api\Data\TotalSegmentInterface
 */
interface TotalSegmentExtensionInterface extends \Magento\Framework\Api\ExtensionAttributesInterface
{
    /**
     * @return string|null
     */
    public function getGiftCards();

    /**
     * @param string $giftCards
     * @return $this
     */
    public function setGiftCards($giftCards);

    /**
     * @return \Magento\Tax\Api\Data\GrandTotalDetailsInterface[]|null
     */
    public function getTaxGrandtotalDetails();

    /**
     * @param \Magento\Tax\Api\Data\GrandTotalDetailsInterface[] $taxGrandtotalDetails
     * @return $this
     */
    public function setTaxGrandtotalDetails($taxGrandtotalDetails);

    /**
     * @return string|null
     */
    public function getGwOrderId();

    /**
     * @param string $gwOrderId
     * @return $this
     */
    public function setGwOrderId($gwOrderId);

    /**
     * @return mixed[]|null
     */
    public function getGwItemIds();

    /**
     * @param mixed[] $gwItemIds
     * @return $this
     */
    public function setGwItemIds($gwItemIds);

    /**
     * @return string|null
     */
    public function getGwAllowGiftReceipt();

    /**
     * @param string $gwAllowGiftReceipt
     * @return $this
     */
    public function setGwAllowGiftReceipt($gwAllowGiftReceipt);

    /**
     * @return string|null
     */
    public function getGwAddCard();

    /**
     * @param string $gwAddCard
     * @return $this
     */
    public function setGwAddCard($gwAddCard);

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
    public function getGwBasePrice();

    /**
     * @param string $gwBasePrice
     * @return $this
     */
    public function setGwBasePrice($gwBasePrice);

    /**
     * @return string|null
     */
    public function getGwItemsPrice();

    /**
     * @param string $gwItemsPrice
     * @return $this
     */
    public function setGwItemsPrice($gwItemsPrice);

    /**
     * @return string|null
     */
    public function getGwItemsBasePrice();

    /**
     * @param string $gwItemsBasePrice
     * @return $this
     */
    public function setGwItemsBasePrice($gwItemsBasePrice);

    /**
     * @return string|null
     */
    public function getGwCardPrice();

    /**
     * @param string $gwCardPrice
     * @return $this
     */
    public function setGwCardPrice($gwCardPrice);

    /**
     * @return string|null
     */
    public function getGwCardBasePrice();

    /**
     * @param string $gwCardBasePrice
     * @return $this
     */
    public function setGwCardBasePrice($gwCardBasePrice);

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
    public function getGwItemsBaseTaxAmount();

    /**
     * @param string $gwItemsBaseTaxAmount
     * @return $this
     */
    public function setGwItemsBaseTaxAmount($gwItemsBaseTaxAmount);

    /**
     * @return string|null
     */
    public function getGwItemsTaxAmount();

    /**
     * @param string $gwItemsTaxAmount
     * @return $this
     */
    public function setGwItemsTaxAmount($gwItemsTaxAmount);

    /**
     * @return string|null
     */
    public function getGwCardBaseTaxAmount();

    /**
     * @param string $gwCardBaseTaxAmount
     * @return $this
     */
    public function setGwCardBaseTaxAmount($gwCardBaseTaxAmount);

    /**
     * @return string|null
     */
    public function getGwCardTaxAmount();

    /**
     * @param string $gwCardTaxAmount
     * @return $this
     */
    public function setGwCardTaxAmount($gwCardTaxAmount);

    /**
     * @return string|null
     */
    public function getGwPriceInclTax();

    /**
     * @param string $gwPriceInclTax
     * @return $this
     */
    public function setGwPriceInclTax($gwPriceInclTax);

    /**
     * @return string|null
     */
    public function getGwBasePriceInclTax();

    /**
     * @param string $gwBasePriceInclTax
     * @return $this
     */
    public function setGwBasePriceInclTax($gwBasePriceInclTax);

    /**
     * @return string|null
     */
    public function getGwCardPriceInclTax();

    /**
     * @param string $gwCardPriceInclTax
     * @return $this
     */
    public function setGwCardPriceInclTax($gwCardPriceInclTax);

    /**
     * @return string|null
     */
    public function getGwCardBasePriceInclTax();

    /**
     * @param string $gwCardBasePriceInclTax
     * @return $this
     */
    public function setGwCardBasePriceInclTax($gwCardBasePriceInclTax);

    /**
     * @return string|null
     */
    public function getGwItemsPriceInclTax();

    /**
     * @param string $gwItemsPriceInclTax
     * @return $this
     */
    public function setGwItemsPriceInclTax($gwItemsPriceInclTax);

    /**
     * @return string|null
     */
    public function getGwItemsBasePriceInclTax();

    /**
     * @param string $gwItemsBasePriceInclTax
     * @return $this
     */
    public function setGwItemsBasePriceInclTax($gwItemsBasePriceInclTax);
}
