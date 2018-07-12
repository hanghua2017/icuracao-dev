<?php
namespace Magento\Rma\Controller\Guest\DelLabel;

/**
 * Interceptor class for @see \Magento\Rma\Controller\Guest\DelLabel
 */
class Interceptor extends \Magento\Rma\Controller\Guest\DelLabel implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Rma\Helper\Data $rmaHelper, \Magento\Sales\Helper\Guest $salesGuestHelper, \Magento\Framework\View\Result\PageFactory $resultPageFactory, \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory, \Magento\Framework\Controller\Result\ForwardFactory $resultForwardFactory)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $rmaHelper, $salesGuestHelper, $resultPageFactory, $resultLayoutFactory, $resultForwardFactory);
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
