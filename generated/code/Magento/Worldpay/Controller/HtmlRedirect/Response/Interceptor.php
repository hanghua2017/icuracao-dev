<?php
namespace Magento\Worldpay\Controller\HtmlRedirect\Response;

/**
 * Interceptor class for @see \Magento\Worldpay\Controller\HtmlRedirect\Response
 */
class Interceptor extends \Magento\Worldpay\Controller\HtmlRedirect\Response implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Worldpay\Gateway\Command\ResponseCommand $command, \Psr\Log\LoggerInterface $logger, \Magento\Framework\View\Result\LayoutFactory $layoutFactory)
    {
        $this->___init();
        parent::__construct($context, $command, $logger, $layoutFactory);
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
