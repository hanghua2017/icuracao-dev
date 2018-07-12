<?php
namespace Magento\CatalogStaging\Controller\Adminhtml\Product\Delete;

/**
 * Interceptor class for @see \Magento\CatalogStaging\Controller\Adminhtml\Product\Delete
 */
class Interceptor extends \Magento\CatalogStaging\Controller\Adminhtml\Product\Delete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Staging\Model\Entity\Update\Delete $stagingUpdateDelete, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($context, $stagingUpdateDelete, $storeManager);
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
