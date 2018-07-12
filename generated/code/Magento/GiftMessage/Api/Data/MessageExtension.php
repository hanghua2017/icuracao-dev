<?php
namespace Magento\GiftMessage\Api\Data;

/**
 * Extension class for @see \Magento\GiftMessage\Api\Data\MessageInterface
 */
class MessageExtension extends \Magento\Framework\Api\AbstractSimpleObject implements MessageExtensionInterface
{
    /**
     * @return string|null
     */
    public function getEntityId()
    {
        return $this->_get('entity_id');
    }

    /**
     * @param string $entityId
     * @return $this
     */
    public function setEntityId($entityId)
    {
        $this->setData('entity_id', $entityId);
        return $this;
    }

    /**
     * @return string|null
     */
    public function getEntityType()
    {
        return $this->_get('entity_type');
    }

    /**
     * @param string $entityType
     * @return $this
     */
    public function setEntityType($entityType)
    {
        $this->setData('entity_type', $entityType);
        return $this;
    }

    /**
     * @return int|null
     */
    public function getWrappingId()
    {
        return $this->_get('wrapping_id');
    }

    /**
     * @param int $wrappingId
     * @return $this
     */
    public function setWrappingId($wrappingId)
    {
        $this->setData('wrapping_id', $wrappingId);
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getWrappingAllowGiftReceipt()
    {
        return $this->_get('wrapping_allow_gift_receipt');
    }

    /**
     * @param bool $wrappingAllowGiftReceipt
     * @return $this
     */
    public function setWrappingAllowGiftReceipt($wrappingAllowGiftReceipt)
    {
        $this->setData('wrapping_allow_gift_receipt', $wrappingAllowGiftReceipt);
        return $this;
    }

    /**
     * @return bool|null
     */
    public function getWrappingAddPrintedCard()
    {
        return $this->_get('wrapping_add_printed_card');
    }

    /**
     * @param bool $wrappingAddPrintedCard
     * @return $this
     */
    public function setWrappingAddPrintedCard($wrappingAddPrintedCard)
    {
        $this->setData('wrapping_add_printed_card', $wrappingAddPrintedCard);
        return $this;
    }
}
