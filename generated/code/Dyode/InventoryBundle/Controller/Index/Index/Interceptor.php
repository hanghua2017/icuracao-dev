<?php
namespace Dyode\InventoryBundle\Controller\Index\Index;

/**
 * Interceptor class for @see \Dyode\InventoryBundle\Controller\Index\Index
 */
class Interceptor extends \Dyode\InventoryBundle\Controller\Index\Index implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Framework\App\Action\Context $context, \Magento\Framework\View\Result\PageFactory $pageFactory, \Dyode\InventoryBundle\Model\Update $update)
    {
        $this->___init();
        parent::__construct($context, $pageFactory, $update);
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
