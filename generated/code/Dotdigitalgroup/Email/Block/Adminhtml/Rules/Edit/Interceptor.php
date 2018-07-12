<?php
namespace Dotdigitalgroup\Email\Block\Adminhtml\Rules\Edit;

/**
 * Interceptor class for @see \Dotdigitalgroup\Email\Block\Adminhtml\Rules\Edit
 */
class Interceptor extends \Dotdigitalgroup\Email\Block\Adminhtml\Rules\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Registry $registry, \Magento\Backend\Block\Widget\Context $context)
    {
        $this->___init();
        parent::__construct($registry, $context);
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
