<?php
namespace Magento\Eway\Controller\Payment\Complete;

/**
 * Interceptor class for @see \Magento\Eway\Controller\Payment\Complete
 */
class Interceptor extends \Magento\Eway\Controller\Payment\Complete implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Payment\Gateway\Command\CommandPoolInterface $commandPool, \Psr\Log\LoggerInterface $logger, \Magento\Framework\View\Result\LayoutFactory $layoutFactory, \Magento\Checkout\Model\Session $checkoutSession, \Magento\Payment\Gateway\Data\PaymentDataObjectFactory $paymentDataObjectFactory, \Magento\Framework\Session\SessionManager $sessionManager)
    {
        $this->___init();
        parent::__construct($context, $commandPool, $logger, $layoutFactory, $checkoutSession, $paymentDataObjectFactory, $sessionManager);
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
