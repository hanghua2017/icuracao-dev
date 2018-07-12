<?php
namespace Magento\Worldpay\Controller\HtmlRedirect\GetTransactionData;

/**
 * Interceptor class for @see \Magento\Worldpay\Controller\HtmlRedirect\GetTransactionData
 */
class Interceptor extends \Magento\Worldpay\Controller\HtmlRedirect\GetTransactionData implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Worldpay\Model\Api\PlaceTransactionService $placeTransactionService, \Magento\Checkout\Model\Session $checkoutSession)
    {
        $this->___init();
        parent::__construct($context, $placeTransactionService, $checkoutSession);
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
