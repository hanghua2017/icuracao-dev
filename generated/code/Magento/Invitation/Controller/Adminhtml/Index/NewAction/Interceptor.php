<?php
namespace Magento\Invitation\Controller\Adminhtml\Index\NewAction;

/**
 * Interceptor class for @see \Magento\Invitation\Controller\Adminhtml\Index\NewAction
 */
class Interceptor extends \Magento\Invitation\Controller\Adminhtml\Index\NewAction implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Invitation\Model\InvitationFactory $invitationFactory, \Magento\Invitation\Model\Config $config, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $invitationFactory, $config, $storeManager);
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
