<?php
namespace Magento\Support\Controller\Adminhtml\Report\View;

/**
 * Interceptor class for @see \Magento\Support\Controller\Adminhtml\Report\View
 */
class Interceptor extends \Magento\Support\Controller\Adminhtml\Report\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Support\Model\ReportFactory $reportFactory, \Magento\Framework\Registry $coreRegistry, \Magento\Support\Model\DataFormatter $dataFormatter, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timeZone)
    {
        $this->___init();
        parent::__construct($context, $reportFactory, $coreRegistry, $dataFormatter, $timeZone);
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
