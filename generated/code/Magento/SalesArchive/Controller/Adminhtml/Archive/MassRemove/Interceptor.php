<?php
namespace Magento\SalesArchive\Controller\Adminhtml\Archive\MassRemove;

/**
 * Interceptor class for @see \Magento\SalesArchive\Controller\Adminhtml\Archive\MassRemove
 */
class Interceptor extends \Magento\SalesArchive\Controller\Adminhtml\Archive\MassRemove implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Ui\Component\MassAction\Filter $filter, \Magento\SalesArchive\Model\Archive $archiveModel, \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($context, $filter, $archiveModel, $collectionFactory);
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
