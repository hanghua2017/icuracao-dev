<?php
namespace Magento\CmsStaging\Controller\Adminhtml\Block\Update\Save;

/**
 * Interceptor class for @see \Magento\CmsStaging\Controller\Adminhtml\Block\Update\Save
 */
class Interceptor extends \Magento\CmsStaging\Controller\Adminhtml\Block\Update\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Staging\Model\Entity\Update\Save $stagingUpdateSave)
    {
        $this->___init();
        parent::__construct($context, $stagingUpdateSave);
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
