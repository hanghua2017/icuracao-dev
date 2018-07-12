<?php
namespace Dyode\ProcessEstimate\Controller\Index\Index;

/**
 * Interceptor class for @see \Dyode\ProcessEstimate\Controller\Index\Index
 */
class Interceptor extends \Dyode\ProcessEstimate\Controller\Index\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $pageFactory, \Dyode\ProcessEstimate\Model\Estimate $estimate)
    {
        $this->___init();
        parent::__construct($context, $pageFactory, $estimate);
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
