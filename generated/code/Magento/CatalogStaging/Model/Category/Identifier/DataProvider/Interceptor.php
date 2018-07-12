<?php
namespace Magento\CatalogStaging\Model\Category\Identifier\DataProvider;

/**
 * Interceptor class for @see \Magento\CatalogStaging\Model\Category\Identifier\DataProvider
 */
class Interceptor extends \Magento\CatalogStaging\Model\Category\Identifier\DataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Magento\Framework\App\RequestInterface $request, \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollectionFactory, array $meta = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $request, $categoryCollectionFactory, $meta, $data);
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
