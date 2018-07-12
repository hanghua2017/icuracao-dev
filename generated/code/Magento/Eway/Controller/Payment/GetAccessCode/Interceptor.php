<?php
namespace Magento\Eway\Controller\Payment\GetAccessCode;

/**
 * Interceptor class for @see \Magento\Eway\Controller\Payment\GetAccessCode
 */
class Interceptor extends \Magento\Eway\Controller\Payment\GetAccessCode implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool, \Psr\Log\LoggerInterface $logger, \Magento\Sales\Api\OrderRepositoryInterface $orderRepository, \Magento\Payment\Gateway\Data\PaymentDataObjectFactory $paymentDataObjectFactory, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Framework\Session\SessionManager $sessionManager)
    {
        $this->___init();
        parent::__construct($context, $commandPool, $logger, $orderRepository, $paymentDataObjectFactory, $checkoutSession, $sessionManager);
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
