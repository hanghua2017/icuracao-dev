<?php
namespace Magento\Support\Controller\Adminhtml\Backup\Download;

/**
 * Interceptor class for @see \Magento\Support\Controller\Adminhtml\Backup\Download
 */
class Interceptor extends \Magento\Support\Controller\Adminhtml\Backup\Download implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Support\Helper\Shell $shellHelper, \Magento\Support\Model\BackupFactory $backupFactory, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \Magento\Framework\Filesystem $filesystem)
    {
        $this->___init();
        parent::__construct($context, $shellHelper, $backupFactory, $fileFactory, $filesystem);
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
