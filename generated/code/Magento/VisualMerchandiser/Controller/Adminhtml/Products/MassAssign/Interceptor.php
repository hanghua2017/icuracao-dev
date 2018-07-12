<?php
namespace Magento\VisualMerchandiser\Controller\Adminhtml\Products\MassAssign;

/**
 * Interceptor class for @see \Magento\VisualMerchandiser\Controller\Adminhtml\Products\MassAssign
 */
class Interceptor extends \Magento\VisualMerchandiser\Controller\Adminhtml\Products\MassAssign implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Catalog\Api\ProductRepositoryInterface $productRepository, \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory, \Magento\Framework\View\LayoutFactory $layoutFactory)
    {
        $this->___init();
        parent::__construct($context, $productRepository, $resultJsonFactory, $layoutFactory);
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
