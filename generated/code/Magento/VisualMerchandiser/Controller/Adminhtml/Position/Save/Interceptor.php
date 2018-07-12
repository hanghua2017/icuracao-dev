<?php
namespace Magento\VisualMerchandiser\Controller\Adminhtml\Position\Save;

/**
 * Interceptor class for @see \Magento\VisualMerchandiser\Controller\Adminhtml\Position\Save
 */
class Interceptor extends \Magento\VisualMerchandiser\Controller\Adminhtml\Position\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\VisualMerchandiser\Model\Position\Cache $cache, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\Serialize\SerializerInterface $serializer = null)
    {
        $this->___init();
        parent::__construct($context, $cache, $resultJsonFactory, $serializer);
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
