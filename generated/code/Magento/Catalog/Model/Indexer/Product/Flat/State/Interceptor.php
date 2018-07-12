<?php
namespace Magento\Catalog\Model\Indexer\Product\Flat\State;

/**
 * Interceptor class for @see \Magento\Catalog\Model\Indexer\Product\Flat\State
 */
class Interceptor extends \Magento\Catalog\Model\Indexer\Product\Flat\State implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry, \Magento\Catalog\Helper\Product\Flat\Indexer $flatIndexerHelper, $isAvailable = false)
    {
        $this->___init();
        parent::__construct($scopeConfig, $indexerRegistry, $flatIndexerHelper, $isAvailable);
    }

    /**
     * {@inheritdoc}
     */
    public function isAvailable()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isAvailable');
        if (!$pluginInfo) {
            return parent::isAvailable();
        } else {
            return $this->___callPlugins('isAvailable', func_get_args(), $pluginInfo);
        }
    }
}
