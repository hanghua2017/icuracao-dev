<?php
namespace Magento\Support\Block\Adminhtml\Backup\Log;

/**
 * Interceptor class for @see \Magento\Support\Block\Adminhtml\Backup\Log
 */
class Interceptor extends \Magento\Support\Block\Adminhtml\Backup\Log implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Support\Model\BackupFactory $backupFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $backupFactory, $data);
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
