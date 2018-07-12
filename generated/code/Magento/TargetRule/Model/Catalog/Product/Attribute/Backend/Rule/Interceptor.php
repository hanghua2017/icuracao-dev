<?php
namespace Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule;

/**
 * Interceptor class for @see \Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule
 */
class Interceptor extends \Magento\TargetRule\Model\Catalog\Product\Attribute\Backend\Rule implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct()
    {
        $this->___init();
    }

    /**
     * {@inheritdoc}
     */
    public function validate($object)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'validate');
        if (!$pluginInfo) {
            return parent::validate($object);
        } else {
            return $this->___callPlugins('validate', func_get_args(), $pluginInfo);
        }
    }
}
