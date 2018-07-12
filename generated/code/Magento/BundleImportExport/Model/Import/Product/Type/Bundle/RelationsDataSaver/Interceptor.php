<?php
namespace Magento\BundleImportExport\Model\Import\Product\Type\Bundle\RelationsDataSaver;

/**
 * Interceptor class for @see \Magento\BundleImportExport\Model\Import\Product\Type\Bundle\RelationsDataSaver
 */
class Interceptor extends \Magento\BundleImportExport\Model\Import\Product\Type\Bundle\RelationsDataSaver implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\ResourceConnection $resource)
    {
        $this->___init();
        parent::__construct($resource);
    }

    /**
     * {@inheritdoc}
     */
    public function saveOptions(array $options)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'saveOptions');
        if (!$pluginInfo) {
            return parent::saveOptions($options);
        } else {
            return $this->___callPlugins('saveOptions', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function saveSelections(array $selections)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'saveSelections');
        if (!$pluginInfo) {
            return parent::saveSelections($selections);
        } else {
            return $this->___callPlugins('saveSelections', func_get_args(), $pluginInfo);
        }
    }
}
