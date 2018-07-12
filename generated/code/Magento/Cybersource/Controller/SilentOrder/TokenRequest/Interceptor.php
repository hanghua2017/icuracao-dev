<?php
namespace Magento\Cybersource\Controller\SilentOrder\TokenRequest;

/**
 * Interceptor class for @see \Magento\Cybersource\Controller\SilentOrder\TokenRequest
 */
class Interceptor extends \Magento\Cybersource\Controller\SilentOrder\TokenRequest implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool, \Magento\Payment\Gateway\Data\PaymentDataObjectFactory $paymentDataObjectFactory, \Magento\Payment\Gateway\ConfigInterface $config, \Magento\Framework\Session\SessionManager $checkoutSession, \Magento\Framework\Controller\Result\JsonFactory $jsonFactory, \Magento\Quote\Api\PaymentMethodManagementInterface $paymentMethodManagement)
    {
        $this->___init();
        parent::__construct($context, $commandPool, $paymentDataObjectFactory, $config, $checkoutSession, $jsonFactory, $paymentMethodManagement);
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
