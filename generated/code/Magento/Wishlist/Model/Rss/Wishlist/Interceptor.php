<?php
namespace Magento\Wishlist\Model\Rss\Wishlist;

/**
 * Interceptor class for @see \Magento\Wishlist\Model\Rss\Wishlist
 */
class Interceptor extends \Magento\Wishlist\Model\Rss\Wishlist implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Wishlist\Helper\Rss $wishlistHelper, \Magento\Wishlist\Block\Customer\Wishlist $wishlistBlock, \Magento\Catalog\Helper\Output $outputHelper, \Magento\Catalog\Helper\Image $imageHelper, \Magento\Framework\UrlInterface $urlBuilder, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\Customer\Model\CustomerFactory $customerFactory, \Magento\Framework\View\LayoutInterface $layout, \Magento\Framework\App\RequestInterface $request)
    {
        $this->___init();
        parent::__construct($wishlistHelper, $wishlistBlock, $outputHelper, $imageHelper, $urlBuilder, $scopeConfig, $eventManager, $customerFactory, $layout, $request);
    }

    /**
     * {@inheritdoc}
     */
    public function getHeader()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getHeader');
        if (!$pluginInfo) {
            return parent::getHeader();
        } else {
            return $this->___callPlugins('getHeader', func_get_args(), $pluginInfo);
        }
    }
}
