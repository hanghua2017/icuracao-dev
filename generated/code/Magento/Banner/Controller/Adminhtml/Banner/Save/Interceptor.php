<?php
namespace Magento\Banner\Controller\Adminhtml\Banner\Save;

/**
 * Interceptor class for @see \Magento\Banner\Controller\Adminhtml\Banner\Save
 */
class Interceptor extends \Magento\Banner\Controller\Adminhtml\Banner\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $registry, \Magento\Banner\Model\Banner\Validator $bannerValidator)
    {
        $this->___init();
        parent::__construct($context, $registry, $bannerValidator);
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
