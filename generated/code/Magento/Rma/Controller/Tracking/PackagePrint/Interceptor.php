<?php
namespace Magento\Rma\Controller\Tracking\PackagePrint;

/**
 * Interceptor class for @see \Magento\Rma\Controller\Tracking\PackagePrint
 */
class Interceptor extends \Magento\Rma\Controller\Tracking\PackagePrint implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\App\Response\Http\FileFactory $fileResponseFactory)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $fileResponseFactory);
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
