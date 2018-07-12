<?php
namespace Magento\Rma\Model\ResourceModel\Item;

/**
 * Interceptor class for @see \Magento\Rma\Model\ResourceModel\Item
 */
class Interceptor extends \Magento\Rma\Model\ResourceModel\Item implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Eav\Model\Entity\Context $context, \Magento\Rma\Helper\Data $rmaData, \Magento\Sales\Model\ResourceModel\Order\Item\CollectionFactory $ordersFactory, \Magento\Catalog\Model\ProductFactory $productFactory, \Magento\Catalog\Model\ProductTypes\ConfigInterface $refundableList, \Magento\Sales\Model\Order\Admin\Item $adminOrderItem, $data = array())
    {
        $this->___init();
        parent::__construct($context, $rmaData, $ordersFactory, $productFactory, $refundableList, $adminOrderItem, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Magento\Framework\Model\AbstractModel $object)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'save');
        if (!$pluginInfo) {
            return parent::save($object);
        } else {
            return $this->___callPlugins('save', func_get_args(), $pluginInfo);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function delete($object)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'delete');
        if (!$pluginInfo) {
            return parent::delete($object);
        } else {
            return $this->___callPlugins('delete', func_get_args(), $pluginInfo);
        }
    }
}
