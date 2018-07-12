<?php
namespace Magento\TargetRule\Block\Catalog\Product\ProductList\Related;

/**
 * Interceptor class for @see \Magento\TargetRule\Block\Catalog\Product\ProductList\Related
 */
class Interceptor extends \Magento\TargetRule\Block\Catalog\Product\ProductList\Related implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\TargetRule\Model\ResourceModel\Index $index, \Magento\TargetRule\Helper\Data $targetRuleData, \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory, \Magento\Catalog\Model\Product\Visibility $visibility, \Magento\TargetRule\Model\IndexFactory $indexFactory, \Magento\Checkout\Model\Cart $cart, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $index, $targetRuleData, $productCollectionFactory, $visibility, $indexFactory, $cart, $data);
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
