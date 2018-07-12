<?php
namespace Magento\SalesArchive\Controller\Adminhtml\Archive\Orders;

/**
 * Interceptor class for @see \Magento\SalesArchive\Controller\Adminhtml\Archive\Orders
 */
class Interceptor extends \Magento\SalesArchive\Controller\Adminhtml\Archive\Orders implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\SalesArchive\Model\Archive $archiveModel, \Magento\Framework\App\Response\Http\FileFactory $fileFactory)
    {
        $this->___init();
        parent::__construct($context, $archiveModel, $fileFactory);
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
