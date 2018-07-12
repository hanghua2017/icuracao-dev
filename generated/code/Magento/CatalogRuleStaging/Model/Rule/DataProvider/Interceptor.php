<?php
namespace Magento\CatalogRuleStaging\Model\Rule\DataProvider;

/**
 * Interceptor class for @see \Magento\CatalogRuleStaging\Model\Rule\DataProvider
 */
class Interceptor extends \Magento\CatalogRuleStaging\Model\Rule\DataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Magento\CatalogRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor, \Magento\Staging\Model\Entity\DataProvider\MetadataProvider $metaDataProvider, array $meta = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $dataPersistor, $metaDataProvider, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getData');
        if (!$pluginInfo) {
            return parent::getData();
        } else {
            return $this->___callPlugins('getData', func_get_args(), $pluginInfo);
        }
    }
}
