<?php
namespace Magento\Invitation\Controller\Index\Send;

/**
 * Interceptor class for @see \Magento\Invitation\Controller\Index\Send
 */
class Interceptor extends \Magento\Invitation\Controller\Index\Send implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Customer\Model\Session $session, \Magento\Invitation\Model\Config $config, \Magento\Invitation\Model\InvitationFactory $invitationFactory, \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator = null, \Magento\Framework\Escaper $escaper = null)
    {
        $this->___init();
        parent::__construct($context, $session, $config, $invitationFactory, $formKeyValidator, $escaper);
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
