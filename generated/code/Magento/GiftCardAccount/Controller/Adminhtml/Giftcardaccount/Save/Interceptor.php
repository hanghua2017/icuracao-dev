<?php
namespace Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount\Save;

/**
 * Interceptor class for @see \Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount\Save
 */
class Interceptor extends \Magento\GiftCardAccount\Controller\Adminhtml\Giftcardaccount\Save implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry, \Magento\Framework\App\Response\Http\FileFactory $fileFactory, \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter, \Magento\GiftCardAccount\Model\EmailManagement $emailManagement)
    {
        $this->___init();
        parent::__construct($context, $coreRegistry, $fileFactory, $dateFilter, $emailManagement);
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
