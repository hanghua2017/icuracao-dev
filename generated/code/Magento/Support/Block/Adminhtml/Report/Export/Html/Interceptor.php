<?php
namespace Magento\Support\Block\Adminhtml\Report\Export\Html;

/**
 * Interceptor class for @see \Magento\Support\Block\Adminhtml\Report\Export\Html
 */
class Interceptor extends \Magento\Support\Block\Adminhtml\Report\Export\Html implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Support\Model\Report\Config $reportConfig, \Magento\Support\Model\Report\DataConverter $dataConverter, \Magento\Support\Model\Report\HtmlGenerator $htmlGenerator, \Magento\Framework\Locale\ResolverInterface $localeResolver, array $data = array())
    {
        $this->___init();
        parent::__construct($context, $reportConfig, $dataConverter, $htmlGenerator, $localeResolver, $data);
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
