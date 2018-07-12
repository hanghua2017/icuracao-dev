<?php
namespace Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation;

/**
 * Interceptor class for @see \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation
 */
class Interceptor extends \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $data);
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
