<?php
namespace Magento\Catalog\Model\ResourceModel\Category\AggregateCount;

/**
 * Interceptor class for @see \Magento\Catalog\Model\ResourceModel\Category\AggregateCount
 */
class Interceptor extends \Magento\Catalog\Model\ResourceModel\Category\AggregateCount implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function processDelete(\Magento\Catalog\Model\Category $category)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'processDelete');
        if (!$pluginInfo) {
            return parent::processDelete($category);
        } else {
            return $this->___callPlugins('processDelete', func_get_args(), $pluginInfo);
        }
    }
}
