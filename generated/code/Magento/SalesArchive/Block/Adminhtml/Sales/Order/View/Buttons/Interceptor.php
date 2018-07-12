<?php
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Buttons;

/**
 * Interceptor class for @see \Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Buttons
 */
class Interceptor extends \Magento\SalesArchive\Block\Adminhtml\Sales\Order\View\Buttons implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, \Magento\Sales\Model\Config $salesConfig, \Magento\Sales\Helper\Reorder $reorderHelper, \Magento\SalesArchive\Model\Config $config, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $registry, $salesConfig, $reorderHelper, $config, $data);
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
