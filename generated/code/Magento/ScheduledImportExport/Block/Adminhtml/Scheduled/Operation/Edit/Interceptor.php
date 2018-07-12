<?php
namespace Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit;

/**
 * Interceptor class for @see \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit
 */
class Interceptor extends \Magento\ScheduledImportExport\Block\Adminhtml\Scheduled\Operation\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\ScheduledImportExport\Model\Scheduled\OperationFactory $operationFactory, \Magento\ScheduledImportExport\Helper\Data $importExportData, \Magento\Framework\Registry $registry, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $operationFactory, $importExportData, $registry, $data);
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
