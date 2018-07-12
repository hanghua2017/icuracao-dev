<?php
namespace Magento\Framework\App\MaintenanceMode;

/**
 * Interceptor class for @see \Magento\Framework\App\MaintenanceMode
 */
class Interceptor extends \Magento\Framework\App\MaintenanceMode implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\Filesystem $filesystem)
    {
        $this->___init();
        parent::__construct($filesystem);
    }

    /**
     * {@inheritdoc}
     */
    public function set($isOn)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'set');
        if (!$pluginInfo) {
            return parent::set($isOn);
        } else {
            return $this->___callPlugins('set', func_get_args(), $pluginInfo);
        }
    }
}
