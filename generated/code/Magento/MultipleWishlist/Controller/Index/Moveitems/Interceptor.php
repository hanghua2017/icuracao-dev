<?php
namespace Magento\MultipleWishlist\Controller\Index\Moveitems;

/**
 * Interceptor class for @see \Magento\MultipleWishlist\Controller\Index\Moveitems
 */
class Interceptor extends \Magento\MultipleWishlist\Controller\Index\Moveitems implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Wishlist\Model\ItemFactory $itemFactory, \Magento\Wishlist\Model\ResourceModel\Wishlist\CollectionFactory $wishlistColFactory, \Magento\Wishlist\Controller\WishlistProviderInterface $wishlistProvider, \Magento\Customer\Model\Session $customerSession, \Magento\MultipleWishlist\Model\ItemManager $itemManager, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator)
    {
        $this->___init();
        parent::__construct($context, $itemFactory, $wishlistColFactory, $wishlistProvider, $customerSession, $itemManager, $formKeyValidator);
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
