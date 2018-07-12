<?php
namespace Magento\Staging\Controller\Adminhtml\Update\Edit;

/**
 * Interceptor class for @see \Magento\Staging\Controller\Adminhtml\Update\Edit
 */
class Interceptor extends \Magento\Staging\Controller\Adminhtml\Update\Edit implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Staging\Api\UpdateRepositoryInterface $updateRepository)
    {
        $this->___init();
        parent::__construct($context, $updateRepository);
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
