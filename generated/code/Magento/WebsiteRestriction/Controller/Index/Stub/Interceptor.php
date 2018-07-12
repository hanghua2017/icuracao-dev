<?php
namespace Magento\WebsiteRestriction\Controller\Index\Stub;

/**
 * Interceptor class for @see \Magento\WebsiteRestriction\Controller\Index\Stub
 */
class Interceptor extends \Magento\WebsiteRestriction\Controller\Index\Stub implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\App\Cache\Type\Config $configCacheType, \Magento\Store\Model\Website $website, \Magento\Cms\Model\PageFactory $pageFactory, \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig, \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $configCacheType, $website, $pageFactory, $scopeConfig, $localeDate);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(\Magento\Framework\App\RequestInterface $request)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'dispatch');
        if (!$pluginInfo) {
            return parent::dispatch($request);
        } else {
            return $this->___callPlugins('dispatch', func_get_args(), $pluginInfo);
        }
    }
}
