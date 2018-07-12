<?php
namespace Magento\Support\Controller\Adminhtml\Backup\Create;

/**
 * Interceptor class for @see \Magento\Support\Controller\Adminhtml\Backup\Create
 */
class Interceptor extends \Magento\Support\Controller\Adminhtml\Backup\Create implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Support\Model\Backup $backupModel, \Magento\Support\Model\ResourceModel\Backup\Collection $backupCollection)
    {
        $this->___init();
        parent::__construct($context, $backupModel, $backupCollection);
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
