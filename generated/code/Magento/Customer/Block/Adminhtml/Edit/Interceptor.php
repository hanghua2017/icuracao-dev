<?php
namespace Magento\Customer\Block\Adminhtml\Edit;

/**
 * Interceptor class for @see \Magento\Customer\Block\Adminhtml\Edit
 */
class Interceptor extends \Magento\Customer\Block\Adminhtml\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, \Magento\Customer\Api\AccountManagementInterface $customerAccountManagement, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Customer\Helper\View $viewHelper, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $registry, $customerAccountManagement, $customerRepository, $viewHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function canRender(\Magento\Backend\Block\Widget\Button\Item $item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'canRender');
        if (!$pluginInfo) {
            return parent::canRender($item);
        } else {
            return $this->___callPlugins('canRender', func_get_args(), $pluginInfo);
        }
    }
}
