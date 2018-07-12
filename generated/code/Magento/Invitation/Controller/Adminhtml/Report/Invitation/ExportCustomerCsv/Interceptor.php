<?php
namespace Magento\Invitation\Controller\Adminhtml\Report\Invitation\ExportCustomerCsv;

/**
 * Interceptor class for @see \Magento\Invitation\Controller\Adminhtml\Report\Invitation\ExportCustomerCsv
 */
class Interceptor extends \Magento\Invitation\Controller\Adminhtml\Report\Invitation\ExportCustomerCsv implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Invitation\Model\Config $config, \Magento\Framework\App\Response\Http\FileFactory $fileFactory)
    {
        $this->___init();
        parent::__construct($context, $config, $fileFactory);
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
