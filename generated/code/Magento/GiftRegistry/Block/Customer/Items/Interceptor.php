<?php
namespace Magento\GiftRegistry\Block\Customer\Items;

/**
 * Interceptor class for @see \Magento\GiftRegistry\Block\Customer\Items
 */
class Interceptor extends \Magento\GiftRegistry\Block\Customer\Items implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Pricing\Helper\Data $pricingHelper, \Magento\GiftRegistry\Model\ItemFactory $itemFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $pricingHelper, $itemFactory, $data);
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
