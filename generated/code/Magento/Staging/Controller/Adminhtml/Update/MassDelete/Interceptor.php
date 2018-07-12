<?php
namespace Magento\Staging\Controller\Adminhtml\Update\MassDelete;

/**
 * Interceptor class for @see \Magento\Staging\Controller\Adminhtml\Update\MassDelete
 */
class Interceptor extends \Magento\Staging\Controller\Adminhtml\Update\MassDelete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository, \Magento\Ui\Component\MassAction\Filter $massActionFilter, \Magento\Staging\Model\ResourceModel\Update\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($context, $updateRepository, $massActionFilter, $collectionFactory);
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
