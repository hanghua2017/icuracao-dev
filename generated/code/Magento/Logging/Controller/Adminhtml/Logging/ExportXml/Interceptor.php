<?php
namespace Magento\Logging\Controller\Adminhtml\Logging\ExportXml;

/**
 * Interceptor class for @see \Magento\Logging\Controller\Adminhtml\Logging\ExportXml
 */
class Interceptor extends \Magento\Logging\Controller\Adminhtml\Logging\ExportXml implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Logging\Model\EventFactory $eventFactory, \Magento\Logging\Model\ArchiveFactory $archiveFactory, \Magento\Framework\App\Response\Http\FileFactory $fileFactory)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $eventFactory, $archiveFactory, $fileFactory);
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
