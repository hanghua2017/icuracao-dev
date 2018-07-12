<?php
namespace Magento\GiftCard\Pricing\Render\FinalPriceBox;

/**
 * Interceptor class for @see \Magento\GiftCard\Pricing\Render\FinalPriceBox
 */
class Interceptor extends \Magento\GiftCard\Pricing\Render\FinalPriceBox implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\View\Element\Template\Context $context, \Magento\Catalog\Model\Product $saleableItem, \Magento\Framework\Pricing\Price\PriceInterface $price, \Magento\Framework\Pricing\Render\RendererPool $rendererPool, \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency, array $data = array(), \Magento\Catalog\Model\Product\Pricing\Renderer\SalableResolverInterface $salableResolver = null, \Magento\Catalog\Pricing\Price\MinimalPriceCalculatorInterface $minimalPriceCalculator = null)
    {
        $this->___init();
        parent::__construct($context, $saleableItem, $price, $rendererPool, $priceCurrency, $data, $salableResolver, $minimalPriceCalculator);
    }

    /**
     * {@inheritdoc}
     */
    public function getCacheKey()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getCacheKey');
        if (!$pluginInfo) {
            return parent::getCacheKey();
        } else {
            return $this->___callPlugins('getCacheKey', func_get_args(), $pluginInfo);
        }
    }
}
