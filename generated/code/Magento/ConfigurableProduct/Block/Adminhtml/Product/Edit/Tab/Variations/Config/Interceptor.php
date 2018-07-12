<?php
namespace Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config;

/**
 * Interceptor class for @see \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config
 */
class Interceptor extends \Magento\ConfigurableProduct\Block\Adminhtml\Product\Edit\Tab\Variations\Config implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $configurableType, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'toHtml');
        if (!$pluginInfo) {
            return parent::toHtml();
        } else {
            return $this->___callPlugins('toHtml', func_get_args(), $pluginInfo);
        }
    }
}
