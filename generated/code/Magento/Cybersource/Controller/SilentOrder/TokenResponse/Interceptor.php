<?php
namespace Magento\Cybersource\Controller\SilentOrder\TokenResponse;

/**
 * Interceptor class for @see \Magento\Cybersource\Controller\SilentOrder\TokenResponse
 */
class Interceptor extends \Magento\Cybersource\Controller\SilentOrder\TokenResponse implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool, \Magento\Payment\Gateway\Data\PaymentDataObjectFactory $paymentDataObjectFactory, \Magento\Framework\View\Result\LayoutFactory $layoutFactory, \Magento\Framework\Registry $registry, \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement, \Magento\Quote\Api\CartRepositoryInterface $cartRepository)
    {
        $this->___init();
        parent::__construct($context, $commandPool, $paymentDataObjectFactory, $layoutFactory, $registry, $paymentMethodManagement, $cartRepository);
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
