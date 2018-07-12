<?php
namespace Magento\Support\Controller\Adminhtml\Report\Delete;

/**
 * Interceptor class for @see \Magento\Support\Controller\Adminhtml\Report\Delete
 */
class Interceptor extends \Magento\Support\Controller\Adminhtml\Report\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Support\Model\ReportFactory $reportFactory)
    {
        $this->___init();
        parent::__construct($context, $reportFactory);
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
