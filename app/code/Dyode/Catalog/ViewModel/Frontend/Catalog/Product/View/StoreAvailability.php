<?php
/**
 * Created by PhpStorm.
 * User: rajeevktomy
 * Date: 26/09/18
 * Time: 11:06 AM
 */

namespace Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View;


use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class StoreAvailability implements ArgumentInterface
{
    const SHIPPING_DAYS_LABEL_CONFIG_PATH = 'curacao_catalog/curacao_product_page/shipping_days_label';

    protected $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function shippingDaysLabel()
    {
        return $this->scopeConfig->getValue(self::SHIPPING_DAYS_LABEL_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
    }
}