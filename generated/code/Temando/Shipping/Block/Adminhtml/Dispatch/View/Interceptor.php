<?php
namespace Temando\Shipping\Block\Adminhtml\Dispatch\View;

/**
 * Interceptor class for @see \Temando\Shipping\Block\Adminhtml\Dispatch\View
 */
class Interceptor extends \Temando\Shipping\Block\Adminhtml\Dispatch\View implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Temando\Shipping\Model\DispatchProviderInterface $dispatchProvider, \Magento\Framework\Stdlib\DateTime\TimezoneInterfaceFactory $timezoneFactory, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $dispatchProvider, $timezoneFactory, $data);
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
