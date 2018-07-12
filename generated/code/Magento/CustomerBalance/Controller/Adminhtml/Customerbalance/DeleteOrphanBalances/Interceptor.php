<?php
namespace Magento\CustomerBalance\Controller\Adminhtml\Customerbalance\DeleteOrphanBalances;

/**
 * Interceptor class for @see \Magento\CustomerBalance\Controller\Adminhtml\Customerbalance\DeleteOrphanBalances
 */
class Interceptor extends \Magento\CustomerBalance\Controller\Adminhtml\Customerbalance\DeleteOrphanBalances implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\CustomerBalance\Model\Balance $balance, \Magento\Customer\Model\CustomerFactory $customerFactory, \Magento\Framework\Registry $coreRegistry)
    {
        $this->___init();
        parent::__construct($context, $balance, $customerFactory, $coreRegistry);
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
