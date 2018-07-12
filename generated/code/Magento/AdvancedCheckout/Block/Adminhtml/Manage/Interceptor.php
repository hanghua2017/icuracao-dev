<?php
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

/**
 * Interceptor class for @see \Magento\AdvancedCheckout\Block\Adminhtml\Manage
 */
class Interceptor extends \Magento\AdvancedCheckout\Block\Adminhtml\Manage implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Json\EncoderInterface $jsonEncoder, \Magento\Framework\Registry $registry, \Magento\Framework\Locale\CurrencyInterface $localeCurrency, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $jsonEncoder, $registry, $localeCurrency, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function canRender(\Magento\Backend\Block\Widget\Button\Item $item)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'canRender');
        if (!$pluginInfo) {
            return parent::canRender($item);
        } else {
            return $this->___callPlugins('canRender', func_get_args(), $pluginInfo);
        }
    }
}
