<?php
namespace Magento\TargetRule\Block\Checkout\Cart\Crosssell;

/**
 * Interceptor class for @see \Magento\TargetRule\Block\Checkout\Cart\Crosssell
 */
class Interceptor extends \Magento\TargetRule\Block\Checkout\Cart\Crosssell implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\TargetRule\Model\ResourceModel\Index $index, \Magento\TargetRule\Helper\Data $targetRuleData, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Model\Product\Visibility $visibility, \Magento\CatalogInventory\Helper\Stock $stockHelper, \Magento\Checkout\Model\Session $session, \Magento\Catalog\Model\Product\LinkFactory $productLinkFactory, \Magento\TargetRule\Model\IndexFactory $indexFactory, \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $index, $targetRuleData, $productCollectionFactory, $visibility, $stockHelper, $session, $productLinkFactory, $indexFactory, $productTypeConfig, $productRepository, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getImage($product, $imageId, $attributes = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getImage');
        if (!$pluginInfo) {
            return parent::getImage($product, $imageId, $attributes);
        } else {
            return $this->___callPlugins('getImage', func_get_args(), $pluginInfo);
        }
    }
}
