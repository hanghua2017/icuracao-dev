<?php
namespace Magento\GiftMessage\Model\ItemRepository;

/**
 * Interceptor class for @see \Magento\GiftMessage\Model\ItemRepository
 */
class Interceptor extends \Magento\GiftMessage\Model\ItemRepository implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Quote\Api\CartRepositoryInterface $quoteRepository, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\GiftMessage\Model\GiftMessageManager $giftMessageManager, \Magento\GiftMessage\Helper\Message $helper, \Magento\GiftMessage\Model\MessageFactory $messageFactory)
    {
        $this->___init();
        parent::__construct($quoteRepository, $storeManager, $giftMessageManager, $helper, $messageFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function save($cartId, \Magento\GiftMessage\Api\Data\MessageInterface $giftMessage, $itemId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save($cartId, $giftMessage, $itemId);
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }
}
