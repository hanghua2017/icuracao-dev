<?php
namespace Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit\AddEventButton;

/**
 * Interceptor class for @see \Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit\AddEventButton
 */
class Interceptor extends \Magento\CatalogEvent\Block\Adminhtml\Catalog\Category\Edit\AddEventButton implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Template\Context $context, \Magento\Catalog\Model\ResourceModel\Category\Tree $categoryTree, \Magento\Framework\Registry $registry, \Magento\Catalog\Model\CategoryFactory $categoryFactory, \Magento\CatalogEvent\Model\ResourceModel\Event\CollectionFactory $collectionFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $categoryTree, $registry, $categoryFactory, $collectionFactory, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getButtonData');
        if (!$pluginInfo) {
            return parent::getButtonData();
        } else {
            return $this->___callPlugins('getButtonData', func_get_args(), $pluginInfo);
        }
    }
}
