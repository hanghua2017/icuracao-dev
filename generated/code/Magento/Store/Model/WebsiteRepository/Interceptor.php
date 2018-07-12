<?php
namespace Magento\Store\Model\WebsiteRepository;

/**
 * Interceptor class for @see \Magento\Store\Model\WebsiteRepository
 */
class Interceptor extends \Magento\Store\Model\WebsiteRepository implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Store\Model\WebsiteFactory $factory, \Magento\Store\Model\ResourceModel\Website\CollectionFactory $websiteCollectionFactory)
    {
        $this->___init();
        parent::__construct($factory, $websiteCollectionFactory);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefault()
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'getDefault');
        if (!$pluginInfo) {
            return parent::getDefault();
        } else {
            return $this->___callPlugins('getDefault', func_get_args(), $pluginInfo);
        }
    }
}
