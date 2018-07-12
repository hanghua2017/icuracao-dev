<?php
namespace Magento\Staging\Model\VersionManager;

/**
 * Interceptor class for @see \Magento\Staging\Model\VersionManager
 */
class Interceptor extends \Magento\Staging\Model\VersionManager implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Staging\Model\UpdateFactory $updateFactory, \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository, \Magento\Framework\App\RequestInterface $request, \Magento\Staging\Model\VersionHistoryInterface $versionHistory)
    {
        $this->___init();
        parent::__construct($updateFactory, $updateRepository, $request, $versionHistory);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentVersionId($versionId)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'setCurrentVersionId');
        if (!$pluginInfo) {
            return parent::setCurrentVersionId($versionId);
        } else {
            return $this->___callPlugins('setCurrentVersionId', func_get_args(), $pluginInfo);
        }
    }
}
