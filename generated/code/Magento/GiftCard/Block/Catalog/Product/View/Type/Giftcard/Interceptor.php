<?php
namespace Magento\GiftCard\Block\Catalog\Product\View\Type\Giftcard;

/**
 * Interceptor class for @see \Magento\GiftCard\Block\Catalog\Product\View\Type\Giftcard
 */
class Interceptor extends \Magento\GiftCard\Block\Catalog\Product\View\Type\Giftcard implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Catalog\Block\Product\Context $context, \Magento\Framework\Stdlib\ArrayUtils $arrayUtils, \Magento\Customer\Model\Session $customerSession, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $arrayUtils, $customerSession, $priceCurrency, $data);
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
