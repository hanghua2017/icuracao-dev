<?php
/**
 * Created by PhpStorm.
 * User: terrific
 * Date: 11/9/18
 * Time: 3:43 PM
 */

namespace Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

class Addtocart implements ArgumentInterface
{
    const QTY_LIMIT_CONFIG_PATH = 'curacao_catalog/curacao_product_page/qty_dropdown_limit';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Addtocart constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Collect Qty select limit from the system configuration settings.
     *
     * @return mixed
     */
    public function getQtyLimit()
    {
        return $this->scopeConfig->getValue(self::QTY_LIMIT_CONFIG_PATH, ScopeInterface::SCOPE_STORE);

    }
}