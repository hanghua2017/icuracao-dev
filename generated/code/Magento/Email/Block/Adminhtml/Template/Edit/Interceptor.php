<?php
namespace Magento\Email\Block\Adminhtml\Template\Edit;

/**
 * Interceptor class for @see \Magento\Email\Block\Adminhtml\Template\Edit
 */
class Interceptor extends \Magento\Email\Block\Adminhtml\Template\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Framework\Registry $registry, \Magento\Backend\Model\Menu\Config $menuConfig, \Magento\Config\Model\Config\Structure $configStructure, \Magento\Email\Model\Template\Config $emailConfig, \Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\Backend\Block\Widget\Button\ButtonList $buttonList, \Magento\Backend\Block\Widget\Button\ToolbarInterface $toolbar, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $registry, $menuConfig, $configStructure, $emailConfig, $jsonHelper, $buttonList, $toolbar, $data);
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
