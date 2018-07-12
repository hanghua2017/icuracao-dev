<?php
namespace Magento\Banner\Block\Widget\Banner;

/**
 * Interceptor class for @see \Magento\Banner\Block\Widget\Banner
 */
class Interceptor extends \Magento\Banner\Block\Widget\Banner implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Banner\Model\ResourceModel\Banner $resource, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $resource, $data);
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
