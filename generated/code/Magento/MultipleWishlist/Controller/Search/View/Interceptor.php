<?php
namespace Magento\MultipleWishlist\Controller\Search\View;

/**
 * Interceptor class for @see \Magento\MultipleWishlist\Controller\Search\View
 */
class Interceptor extends \Magento\MultipleWishlist\Controller\Search\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Wishlist\Model\ItemFactory $itemFactory, \Magento\Wishlist\Model\WishlistFactory $wishlistFactory, \Magento\MultipleWishlist\Model\SearchFactory $searchFactory, \Magento\MultipleWishlist\Model\Search\Strategy\EmailFactory $strategyEmailFactory, \Magento\MultipleWishlist\Model\Search\Strategy\NameFactory $strategyNameFactory, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Checkout\Model\Cart $checkoutCart, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Locale\ResolverInterface $localeResolver, \Magento\Framework\Module\Manager $moduleManager)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $itemFactory, $wishlistFactory, $searchFactory, $strategyEmailFactory, $strategyNameFactory, $checkoutSession, $checkoutCart, $customerSession, $localeResolver, $moduleManager);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
