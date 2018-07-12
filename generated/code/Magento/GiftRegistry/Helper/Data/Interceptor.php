<?php
namespace Magento\GiftRegistry\Helper\Data;

/**
 * Interceptor class for @see \Magento\GiftRegistry\Helper\Data
 */
class Interceptor extends \Magento\GiftRegistry\Helper\Data implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Customer\Model\Session $customerSession, \Magento\GiftRegistry\Model\EntityFactory $entityFactory, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \Magento\Framework\Escaper $escaper, \Magento\Framework\Locale\ResolverInterface $localeResolver, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($context, $customerSession, $entityFactory, $localeDate, $escaper, $localeResolver, $storeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function canAddToGiftRegistry($item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'canAddToGiftRegistry');
        if (!$pluginInfo) {
            return parent::canAddToGiftRegistry($item);
        } else {
            return $this->___callPlugins('canAddToGiftRegistry', func_get_args(), $pluginInfo);
        }
    }
}
