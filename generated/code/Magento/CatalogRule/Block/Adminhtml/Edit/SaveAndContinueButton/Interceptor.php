<?php
namespace Magento\CatalogRule\Block\Adminhtml\Edit\SaveAndContinueButton;

/**
 * Interceptor class for @see \Magento\CatalogRule\Block\Adminhtml\Edit\SaveAndContinueButton
 */
class Interceptor extends \Magento\CatalogRule\Block\Adminhtml\Edit\SaveAndContinueButton implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry)
    {
        $this->___init();
        parent::__construct($context, $registry);
    }

    /**
     * {@inheritdoc}
     */
    public function canRender($name)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'canRender');
        if (!$pluginInfo) {
            return parent::canRender($name);
        } else {
            return $this->___callPlugins('canRender', func_get_args(), $pluginInfo);
        }
    }
}
