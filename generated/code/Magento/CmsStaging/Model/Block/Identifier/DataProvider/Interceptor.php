<?php
namespace Magento\CmsStaging\Model\Block\Identifier\DataProvider;

/**
 * Interceptor class for @see \Magento\CmsStaging\Model\Block\Identifier\DataProvider
 */
class Interceptor extends \Magento\CmsStaging\Model\Block\Identifier\DataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Magento\Cms\Model\ResourceModel\Block\CollectionFactory $blockCollectionFactory, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor, array $meta = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $blockCollectionFactory, $dataPersistor, $meta, $data);
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
