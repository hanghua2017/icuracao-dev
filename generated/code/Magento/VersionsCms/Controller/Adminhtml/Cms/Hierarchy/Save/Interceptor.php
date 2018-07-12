<?php
namespace Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy\Save;

/**
 * Interceptor class for @see \Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy\Save
 */
class Interceptor extends \Magento\VersionsCms\Controller\Adminhtml\Cms\Hierarchy\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $storeManager);
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
