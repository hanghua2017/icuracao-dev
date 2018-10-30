<?php

namespace Dyode\OrderDetails\Block\Onepage;

use Aheadworks\StoreLocator\Model\LocationFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Success extends \Magento\Checkout\Block\Onepage\Success {

    protected $orderRepository;
    protected $renderer;
    protected $order;

    const ADS_MOMENTUM_DELIVERY_MSG_CONFIG_PATH = 'carriers/adsmomentum/delivery_message';
    const PILOT_DELIVERY_MSG_CONFIG_PATH = 'carriers/pilot/delivery_message';
    const UPS_DELIVERY_MSG_CONFIG_PATH = 'carriers/ups/delivery_message';
    const USPS_DELIVERY_MSG_CONFIG_PATH = 'carriers/usps/delivery_message';

     /**
     * @var \Aheadworks\StoreLocator\Model\LocationFactory
     */
    protected $locationFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
    */
    protected $scopeConfig;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Sales\Model\Order\Config $orderConfig
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Aheadworks\StoreLocator\Model\LocationFactory $locationFactory
     * @param array $data
     */
    public function __construct(
      \Magento\Framework\View\Element\Template\Context $context,
      \Magento\Checkout\Model\Session $checkoutSession,
      \Magento\Sales\Model\Order\Config $orderConfig,
      \Magento\Framework\App\Http\Context $httpContext,
      \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
      \Magento\Sales\Model\Order\Address\Renderer $renderer,
      \Magento\Sales\Api\Data\OrderInterface $order,
      LocationFactory $locationFactory,
      ScopeConfigInterface $scopeConfig,
      array $data = []
    ) {
        $this->orderRepository = $orderRepository;
        $this->renderer = $renderer;
        $this->order = $order;
        $this->locationFactory = $locationFactory;
        $this->scopeConfig = $scopeConfig;
        parent::__construct(
                $context, $checkoutSession, $orderConfig, $httpContext, $data
        );
    }

    public function getOrder($id) {
       // return $this->orderRepository->get($id);
       return $this->order->loadByIncrementId($id);
    }

    public function getFormatedAddress($address) {
        return $this->renderer->format($address, 'html');
    }

    public function getPaymentMethodtitle($order) {
        $payment = $order->getPayment();
        $method = $payment->getMethodInstance();
        return $method->getTitle();
    }

    /**
     * Retrieve store location Details.
     *
     * @param $$locId
     * @return mixed|\Aheadworks\StoreLocator\Model\Location|null
     */
  
    public function getPickupLocation($locId){
       return $this->locationFactory->create()->load($locId,'store_location_code');
    }

      /**
     * Collect shipping method delivery messages from system configuration.
     *
     * @return array
     */
    public function collectShippingMethodDeliveryMsgs()
    {
        return [
            'adsmomentum' => $this->getConfigValue(self::ADS_MOMENTUM_DELIVERY_MSG_CONFIG_PATH),
            'pilot'       => $this->getConfigValue(self::PILOT_DELIVERY_MSG_CONFIG_PATH),
            'ups'         => $this->getConfigValue(self::UPS_DELIVERY_MSG_CONFIG_PATH),
            'usps'        => $this->getConfigValue(self::USPS_DELIVERY_MSG_CONFIG_PATH),
        ];
    }

    /**
     * Collect a configuration value corresponding to the config path given against the store.
     *
     * @param string $configPath
     * @return string
     */
    protected function getConfigValue($configPath)
    {
        return $this->scopeConfig->getValue($configPath, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }
}
