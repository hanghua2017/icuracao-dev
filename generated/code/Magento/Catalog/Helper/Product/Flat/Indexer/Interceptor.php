<?php
namespace Magento\Catalog\Helper\Product\Flat\Indexer;

/**
 * Interceptor class for @see \Magento\Catalog\Helper\Product\Flat\Indexer
 */
class Interceptor extends \Magento\Catalog\Helper\Product\Flat\Indexer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Helper\Context $context, \Magento\Framework\App\ResourceConnection $resource, \Magento\Eav\Model\Config $eavConfig, \Magento\Catalog\Model\Attribute\Config $attributeConfig, \Magento\Catalog\Model\ResourceModel\ConfigFactory $configFactory, \Magento\Eav\Model\Entity\AttributeFactory $attributeFactory, \Magento\Store\Model\StoreManagerInterface $storeManager, \Magento\Framework\Mview\View\Changelog $changelog, $addFilterableAttrs = false, $addChildData = false, $flatAttributeGroups = array())
    {
        $this->___init();
        parent::__construct($context, $resource, $eavConfig, $attributeConfig, $configFactory, $attributeFactory, $storeManager, $changelog, $addFilterableAttrs, $addChildData, $flatAttributeGroups);
    }

    /**
     * {@inheritdoc}
     */
    public function getFlatColumnsDdlDefinition()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFlatColumnsDdlDefinition');
        if (!$pluginInfo) {
            return parent::getFlatColumnsDdlDefinition();
        } else {
            return $this->___callPlugins('getFlatColumnsDdlDefinition', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getFlatIndexes()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getFlatIndexes');
        if (!$pluginInfo) {
            return parent::getFlatIndexes();
        } else {
            return $this->___callPlugins('getFlatIndexes', func_get_args(), $pluginInfo);
        }
    }
}
