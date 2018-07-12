<?php
namespace Magento\CatalogImportExport\Model\Import\Product;

/**
 * Interceptor class for @see \Magento\CatalogImportExport\Model\Import\Product
 */
class Interceptor extends \Magento\CatalogImportExport\Model\Import\Product implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Json\Helper\Data $jsonHelper, \Magento\ImportExport\Helper\Data $importExportData, \Magento\ImportExport\Model\ResourceModel\Import\Data $importData, \Magento\Eav\Model\Config $config, \Magento\Framework\App\ResourceConnection $resource, \Magento\ImportExport\Model\ResourceModel\Helper $resourceHelper, \Magento\Framework\Stdlib\StringUtils $string, \Magento\ImportExport\Model\Import\ErrorProcessing\ProcessingErrorAggregatorInterface $errorAggregator, \Magento\Framework\Event\ManagerInterface $eventManager, \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry, \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration, \Magento\CatalogInventory\Model\Spi\StockStateProviderInterface $stockStateProvider, \Magento\Catalog\Helper\Data $catalogData, \Magento\ImportExport\Model\Import\Config $importConfig, \Magento\CatalogImportExport\Model\Import\Proxy\Product\ResourceModelFactory $resourceFactory, \Magento\CatalogImportExport\Model\Import\Product\OptionFactory $optionFactory, \Magento\Eav\Model\ResourceModel\Entity\Attribute\Set\CollectionFactory $setColFactory, \Magento\CatalogImportExport\Model\Import\Product\Type\Factory $productTypeFactory, \Magento\Catalog\Model\ResourceModel\Product\LinkFactory $linkFactory, \Magento\CatalogImportExport\Model\Import\Proxy\ProductFactory $proxyProdFactory, \Magento\CatalogImportExport\Model\Import\UploaderFactory $uploaderFactory, \Magento\Framework\Filesystem $filesystem, \Magento\CatalogInventory\Model\ResourceModel\Stock\ItemFactory $stockResItemFac, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate, \Magento\Framework\Stdlib\DateTime $dateTime, \Psr\Log\LoggerInterface $logger, \Magento\Framework\Indexer\IndexerRegistry $indexerRegistry, \Magento\CatalogImportExport\Model\Import\Product\StoreResolver $storeResolver, \Magento\CatalogImportExport\Model\Import\Product\SkuProcessor $skuProcessor, \Magento\CatalogImportExport\Model\Import\Product\CategoryProcessor $categoryProcessor, \Magento\CatalogImportExport\Model\Import\Product\Validator $validator, \Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor $objectRelationProcessor, \Magento\Framework\Model\ResourceModel\Db\TransactionManagerInterface $transactionManager, \Magento\CatalogImportExport\Model\Import\Product\TaxClassProcessor $taxClassProcessor, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Catalog\Model\Product\Url $productUrl, array $data = array(), array $dateAttrCodes = array(), \Magento\Catalog\Model\Config $catalogConfig = null)
    {
        $this->___init();
        parent::__construct($jsonHelper, $importExportData, $importData, $config, $resource, $resourceHelper, $string, $errorAggregator, $eventManager, $stockRegistry, $stockConfiguration, $stockStateProvider, $catalogData, $importConfig, $resourceFactory, $optionFactory, $setColFactory, $productTypeFactory, $linkFactory, $proxyProdFactory, $uploaderFactory, $filesystem, $stockResItemFac, $localeDate, $dateTime, $logger, $indexerRegistry, $storeResolver, $skuProcessor, $categoryProcessor, $validator, $objectRelationProcessor, $transactionManager, $taxClassProcessor, $scopeConfig, $productUrl, $data, $dateAttrCodes, $catalogConfig);
    }

    /**
     * {@inheritdoc}
     */
    public function saveProductEntity(array $entityRowsIn, array $entityRowsUp)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'saveProductEntity');
        if (!$pluginInfo) {
            return parent::saveProductEntity($entityRowsIn, $entityRowsUp);
        } else {
            return $this->___callPlugins('saveProductEntity', func_get_args(), $pluginInfo);
        }
    }
}
