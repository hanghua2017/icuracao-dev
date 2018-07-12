<?php
namespace Magento\CatalogStaging\Model\Product\DataProvider;

/**
 * Interceptor class for @see \Magento\CatalogStaging\Model\Product\DataProvider
 */
class Interceptor extends \Magento\CatalogStaging\Model\Product\DataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory, \Magento\Ui\DataProvider\Modifier\PoolInterface $pool, \Magento\Staging\Model\Entity\DataProvider\MetadataProvider $metaDataProvider, array $meta = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $pool, $metaDataProvider, $meta, $data);
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
