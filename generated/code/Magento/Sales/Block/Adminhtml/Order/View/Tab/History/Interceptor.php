<?php
namespace Magento\Sales\Block\Adminhtml\Order\View\Tab\History;

/**
 * Interceptor class for @see \Magento\Sales\Block\Adminhtml\Order\View\Tab\History
 */
class Interceptor extends \Magento\Sales\Block\Adminhtml\Order\View\Tab\History implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Sales\Helper\Admin $adminHelper, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $registry, $adminHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getFullHistory()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFullHistory');
        if (!$pluginInfo) {
            return parent::getFullHistory();
        } else {
            return $this->___callPlugins('getFullHistory', func_get_args(), $pluginInfo);
        }
    }
}
