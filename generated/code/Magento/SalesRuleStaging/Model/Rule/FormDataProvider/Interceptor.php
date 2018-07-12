<?php
namespace Magento\SalesRuleStaging\Model\Rule\FormDataProvider;

/**
 * Interceptor class for @see \Magento\SalesRuleStaging\Model\Rule\FormDataProvider
 */
class Interceptor extends \Magento\SalesRuleStaging\Model\Rule\FormDataProvider implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct($name, $primaryFieldName, $requestFieldName, \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory, \Magento\Framework\Registry $registry, \Magento\SalesRule\Model\Rule\Metadata\ValueProvider $metadataValueProvider, \Magento\Staging\Model\Entity\DataProvider\MetadataProvider $metaDataProvider, \Magento\SalesRule\Model\RuleFactory $salesRuleFactory, \Magento\Framework\App\RequestInterface $request, array $meta = array(), array $data = array(), array $ignoredFields = array())
    {
        $this->___init();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $collectionFactory, $registry, $metadataValueProvider, $metaDataProvider, $salesRuleFactory, $request, $meta, $data, $ignoredFields);
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
