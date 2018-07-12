<?php
namespace Magento\Checkout\Model\Cart\CollectQuote;

/**
 * Interceptor class for @see \Magento\Checkout\Model\Cart\CollectQuote
 */
class Interceptor extends \Magento\Checkout\Model\Cart\CollectQuote implements \Magento\Framework\Interception\InterceptorInterface
{
    use \Magento\Framework\Interception\Interceptor;

    public function __construct(\Magento\Customer\Model\Session $customerSession, \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository, \Magento\Customer\Api\AddressRepositoryInterface $addressRepository, \Magento\Quote\Api\Data\EstimateAddressInterfaceFactory $estimatedAddressFactory, \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManager, \Magento\Quote\Api\CartRepositoryInterface $quoteRepository)
    {
        $this->___init();
        parent::__construct($customerSession, $customerRepository, $addressRepository, $estimatedAddressFactory, $shippingMethodManager, $quoteRepository);
    }

    /**
     * {@inheritdoc}
     */
    public function collect(\Magento\Quote\Model\Quote $quote)
    {
        $pluginInfo = $this->pluginList->getNext($this->subjectType, 'collect');
        if (!$pluginInfo) {
            return parent::collect($quote);
        } else {
            return $this->___callPlugins('collect', func_get_args(), $pluginInfo);
        }
    }
}
