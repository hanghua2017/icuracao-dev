<?php
namespace Magento\CustomerImportExport\Model\Import\Customer;

/**
 * Interceptor class for @see \Magento\CustomerImportExport\Model\Import\Customer
 */
class Interceptor extends \Magento\CustomerImportExport\Model\Import\Customer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Stdlib\StringUtils $string, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\ImportExport\Model\ImportFactory $importFactory, \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper, \Magento\Framework\App\ResourceConnection $resource, \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\ImportExport\Model\Export\Factory $collectionFactory, \Magento\Eav\Model\Config $eavConfig, \Magento\CustomerImportExport\Model\ResourceModel\Import\Customer\StorageFactory $storageFactory, \Magento\Customer\Model\ResourceModel\Attribute\CollectionFactory $attrCollectionFactory, \Magento\Customer\Model\CustomerFactory $customerFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($string, $scopeConfig, $importFactory, $resourceHelper, $resource, $errorAggregator, $storeManager, $collectionFactory, $eavConfig, $storageFactory, $attrCollectionFactory, $customerFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getValidColumnNames()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getValidColumnNames');
        if (!$pluginInfo) {
            return parent::getValidColumnNames();
        } else {
            return $this->___callPlugins('getValidColumnNames', func_get_args(), $pluginInfo);
        }
    }
}
