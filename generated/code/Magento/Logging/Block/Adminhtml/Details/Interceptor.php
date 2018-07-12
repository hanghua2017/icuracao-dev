<?php
namespace Magento\Logging\Block\Adminhtml\Details;

/**
 * Interceptor class for @see \Magento\Logging\Block\Adminhtml\Details
 */
class Interceptor extends \Magento\Logging\Block\Adminhtml\Details implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\Block\Widget\Context $context, \Magento\Framework\Registry $registry, \Magento\User\Model\UserFactory $userFactory, array $data = array(), \Magento\Framework\Serialize\Serializer\Json $json = null)
    {
        $this->___init();
        parent::__construct($context, $registry, $userFactory, $data, $json);
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
