<?php
namespace Magento\GiftCard\Model\Product\SaveHandler;

/**
 * Interceptor class for @see \Magento\GiftCard\Model\Product\SaveHandler
 */
class Interceptor extends \Magento\GiftCard\Model\Product\SaveHandler implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\EntityManager\MetadataPool $metadataPool, \Magento\GiftCard\Model\Giftcard\AmountRepository $giftcardAmountRepository, \Magento\Catalog\Api\ProductAttributeRepositoryInterface $attributeRepository, \Magento\GiftCard\Api\Data\GiftcardAmountInterfaceFactory $giftcardAmountFactory, \Magento\GiftCard\Model\ResourceModel\Db\GetAmountIdsByProduct $getAmountIdsByProduct, \Magento\GiftCard\Model\Product\ReadHandler $readHandler, \Magento\Store\Model\StoreManagerInterface $storeManager)
    {
        $this->___init();
        parent::__construct($metadataPool, $giftcardAmountRepository, $attributeRepository, $giftcardAmountFactory, $getAmountIdsByProduct, $readHandler, $storeManager);
    }

    /**
     * {@inheritdoc}
     */
    public function execute($entity, $arguments = array())
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'execute');
        if (!$pluginInfo) {
            return parent::execute($entity, $arguments);
        } else {
            return $this->___callPlugins('execute', func_get_args(), $pluginInfo);
        }
    }
}
