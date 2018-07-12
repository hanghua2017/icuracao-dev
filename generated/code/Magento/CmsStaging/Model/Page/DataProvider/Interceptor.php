<?php
namespace Magento\CmsStaging\Model\Page\DataProvider;

/**
 * Interceptor class for @see \Magento\CmsStaging\Model\Page\DataProvider
 */
class Interceptor extends \Magento\CmsStaging\Model\Page\DataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Magento\Cms\Model\ResourceModel\Page\CollectionFactory $pageCollectionFactory, \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor, \Magento\Staging\Model\Entity\DataProvider\MetadataProvider $metadataProvider, array $meta = array(), array $data = array())
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $pageCollectionFactory, $dataPersistor, $metadataProvider, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareMeta(array $meta)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'prepareMeta');
        if (!$pluginInfo) {
            return parent::prepareMeta($meta);
        } else {
            return $this->___callPlugins('prepareMeta', func_get_args(), $pluginInfo);
        }
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
