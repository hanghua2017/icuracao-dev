<?php
namespace Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitation;

/**
 * Interceptor class for @see \Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitation
 */
class Interceptor extends \Magento\Catalog\Model\ResourceModel\Product\Collection\ProductLimitation implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function isUsingPriceIndex()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'isUsingPriceIndex');
        if (!$pluginInfo) {
            return parent::isUsingPriceIndex();
        } else {
            return $this->___callPlugins('isUsingPriceIndex', func_get_args(), $pluginInfo);
        }
    }
}
