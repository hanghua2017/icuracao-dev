<?php
namespace Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit;

/**
 * Interceptor class for @see \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit
 */
class Interceptor extends \Magento\VersionsCms\Block\Adminhtml\Cms\Hierarchy\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\VersionsCms\Helper\Data $cmsData, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $cmsData, $data);
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
