<?php

namespace Dyode\Checkout\Helper;

use Magento\Cms\Helper\Page as PageHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

class CheckoutConfigHelper
{

    const TC_CONFIG_PATH = 'curacao_checkout/curacao_onepage_checkout/terms_and_conditions_cms_page';
    const PRIVACY_CONFIG_PATH = 'curacao_checkout/curacao_onepage_checkout/privacy_cms_page';
    const ADS_MOMENTUM_DELIVERY_MSG_CONFIG_PATH = 'carriers/adsmomentum/delivery_message';
    const PILOT_DELIVERY_MSG_CONFIG_PATH = 'carriers/pilot/delivery_message';
    const UPS_DELIVERY_MSG_CONFIG_PATH = 'carriers/ups/delivery_message';
    const USPS_DELIVERY_MSG_CONFIG_PATH = 'carriers/usps/delivery_message';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    protected $_pageHelper;

    /**
     * CheckoutConfigHelper constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Cms\Helper\Page $pageHelper
     */
    public function __construct(ScopeConfigInterface $scopeConfig, PageHelper $pageHelper)
    {
        $this->scopeConfig = $scopeConfig;
        $this->_pageHelper = $pageHelper;
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
     * Provide terms and condition cms page for the checkout page, which is configured in the System > Configuration
     *
     * @return string
     */
    public function termsAndConditionsLink()
    {
        $pageId = $this->scopeConfig->getValue(self::TC_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
        return $this->_pageHelper->getPageUrl($pageId);
    }

    /**
     * Provide privacy link cms page for the checkout page, which is configured in the System > Configuration
     *
     * @return string
     */
    public function checkoutPrivacyLink()
    {
        $pageId = $this->scopeConfig->getValue(self::PRIVACY_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
        return $this->_pageHelper->getPageUrl($pageId);
    }

    /**
     * Collect a configuration value corresponding to the config path given against the store.
     *
     * @param string $configPath
     * @return string
     */
    protected function getConfigValue($configPath)
    {
        return $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE);
    }
}