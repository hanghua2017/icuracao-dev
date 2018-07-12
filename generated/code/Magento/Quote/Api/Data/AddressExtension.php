<?php
namespace Magento\Quote\Api\Data;

/**
 * Extension class for @see \Magento\Quote\Api\Data\AddressInterface
 */
class AddressExtension extends \Magento\Framework\Api\AbstractSimpleObject implements AddressExtensionInterface
{
    /**
     * @return int|null
     */
    public function getGiftRegistryId()
    {
        return $this->_get('gift_registry_id');
    }

    /**
     * @param int $giftRegistryId
     * @return $this
     */
    public function setGiftRegistryId($giftRegistryId)
    {
        $this->setData('gift_registry_id', $giftRegistryId);
        return $this;
    }

    /**
     * @return \Magento\Framework\Api\AttributeInterface[]|null
     */
    public function getCheckoutFields()
    {
        return $this->_get('checkout_fields');
    }

    /**
     * @param \Magento\Framework\Api\AttributeInterface[] $checkoutFields
     * @return $this
     */
    public function setCheckoutFields($checkoutFields)
    {
        $this->setData('checkout_fields', $checkoutFields);
        return $this;
    }
}
