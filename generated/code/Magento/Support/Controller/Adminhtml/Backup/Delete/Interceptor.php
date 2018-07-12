<?php
namespace Magento\Support\Controller\Adminhtml\Backup\Delete;

/**
 * Interceptor class for @see \Magento\Support\Controller\Adminhtml\Backup\Delete
 */
class Interceptor extends \Magento\Support\Controller\Adminhtml\Backup\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Support\Model\BackupFactory $backupFactory)
    {
        $this->___init();
        parent::__construct($context, $backupFactory);
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
