<?php
namespace Magento\Sales\Cron\CleanExpiredQuotes;

/**
 * Interceptor class for @see \Magento\Sales\Cron\CleanExpiredQuotes
 */
class Interceptor extends \Magento\Sales\Cron\CleanExpiredQuotes implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\Model\StoresConfig $storesConfig, \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $collectionFactory)
    {
        $this->___init();
        parent::__construct($storesConfig, $collectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            return parent::execute();
        } else {
            return $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }
}
