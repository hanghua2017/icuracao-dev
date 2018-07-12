<?php
namespace Magento\GiftRegistry\Block\Wishlist\View;

/**
 * Interceptor class for @see \Magento\GiftRegistry\Block\Wishlist\View
 */
class Interceptor extends \Magento\GiftRegistry\Block\Wishlist\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\App\Http\Context $httpContext, \Magento\Catalog\Helper\Product\ConfigurationPool $helperPool, \Magento\Customer\Helper\Session\CurrentCustomer $currentCustomer, \Magento\Framework\Data\Helper\PostHelper $postDataHelper, \Magento\GiftRegistry\Helper\Data $giftRegistryData, \Magento\Framework\Module\Manager $moduleManager, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $httpContext, $helperPool, $currentCustomer, $postDataHelper, $giftRegistryData, $moduleManager, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage($product, $imageId, $attributes = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImage');
        if (!$pluginInfo) {
            return parent::getImage($product, $imageId, $attributes);
        } else {
            return $this->___callPlugins('getImage', func_get_args(), $pluginInfo);
        }
    }
}
