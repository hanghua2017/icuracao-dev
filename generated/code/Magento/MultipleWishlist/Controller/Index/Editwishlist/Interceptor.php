<?php
namespace Magento\MultipleWishlist\Controller\Index\Editwishlist;

/**
 * Interceptor class for @see \Magento\MultipleWishlist\Controller\Index\Editwishlist
 */
class Interceptor extends \Magento\MultipleWishlist\Controller\Index\Editwishlist implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\MultipleWishlist\Model\WishlistEditor $wishlistEditor, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator)
    {
        $this->___init();
        parent::__construct($context, $wishlistEditor, $customerSession, $formKeyValidator);
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
