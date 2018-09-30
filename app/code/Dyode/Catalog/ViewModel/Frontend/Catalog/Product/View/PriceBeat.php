<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package Dyode
 * @module  Dyode_Catalog
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace  Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View;

use Magento\Cms\Helper\Page;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Price Beat View Model
 */
class PriceBeat implements ArgumentInterface
{

    const CMS_PAGE_CONFIG_PATH = 'curacao_catalog/curacao_product_page/price_beat_cms_page';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    protected $pageHelper;

    /**
     * PriceBeat constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Cms\Helper\Page                           $pageHelper
     */
    public function __construct(ScopeConfigInterface $scopeConfig, Page $pageHelper)
    {
        $this->scopeConfig = $scopeConfig;
        $this->pageHelper = $pageHelper;
    }

    /**
     * Provide Price Beat cms page url
     *
     * @return string
     */
    public function getPriceBeatCmsPageUrl()
    {
        $pageId = $this->scopeConfig->getValue(self::CMS_PAGE_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
        return $this->pageHelper->getPageUrl($pageId);
    }
}
