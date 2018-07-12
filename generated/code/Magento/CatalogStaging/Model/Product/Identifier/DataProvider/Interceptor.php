<?php
namespace Magento\CatalogStaging\Model\Product\Identifier\DataProvider;

/**
 * Interceptor class for @see \Magento\CatalogStaging\Model\Product\Identifier\DataProvider
 */
class Interceptor extends \Magento\CatalogStaging\Model\Product\Identifier\DataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $collectionFactory, \Magento\Ui\DataProvider\Modifier\PoolInterface $pool, \Magento\Framework\App\RequestInterface $request, array $meta = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $pool, $request, $meta, $data);
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
