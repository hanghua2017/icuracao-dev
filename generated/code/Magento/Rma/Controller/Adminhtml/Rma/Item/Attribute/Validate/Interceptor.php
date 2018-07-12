<?php
namespace Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute\Validate;

/**
 * Interceptor class for @see \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute\Validate
 */
class Interceptor extends \Magento\Rma\Controller\Adminhtml\Rma\Item\Attribute\Validate implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Store\Model\WebsiteFactory $websiteFactory)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $websiteFactory);
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
