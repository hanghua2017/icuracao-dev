<?php
namespace Magento\Rma\Controller\Adminhtml\Rma\RmaCustomer;

/**
 * Interceptor class for @see \Magento\Rma\Controller\Adminhtml\Rma\RmaCustomer
 */
class Interceptor extends \Magento\Rma\Controller\Adminhtml\Rma\RmaCustomer implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \Magento\Framework\Filesystem $filesystem, \Magento\Shipping\Helper\Carrier $carrierHelper, \Magento\Rma\Model\Shipping\LabelService $labelService, \Magento\Rma\Model\Rma\RmaDataMapper $rmaDataMapper)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $fileFactory, $filesystem, $carrierHelper, $labelService, $rmaDataMapper);
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
