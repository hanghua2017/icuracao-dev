<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       02/08/2018
 */

namespace Dyode\Checkout\Model;

use Dyode\Checkout\Helper\CuracaoHelper;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\Cms\Helper\Page as PageHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\View\Asset\Repository as AssetRepository;

class ConfigProvider implements ConfigProviderInterface
{
    const TC_CONFIG_PATH = 'curacao_checkout/curacao_onepage_checkout/terms_and_conditions_cms_page';
    const PRIVACY_CONFIG_PATH = 'curacao_checkout/curacao_onepage_checkout/privacy_cms_page';

    /** @var LayoutInterface */
    protected $_layout;
    protected $_customerSession;
    protected $_helper;
    protected $_customerRepositoryInterface;
    protected $_curacaoId;
    protected $_cart;
    protected $_priceHelper;
    protected $_canApply;
    protected $_linked;
    protected $_cmsBlock;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \Magento\Cms\Helper\Page
     */
    protected $_pageHelper;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepository;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * ConfigProvider constructor.
     *
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Cart $cart
     * @param \Dyode\ARWebservice\Helper\Data $helper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Cms\Helper\Page $pageHelper
     * @param \Magento\Framework\View\Asset\Repository $assetRepository
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     * @param $blockId
     */
    public function __construct(
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Cart $cart,
        \Dyode\ARWebservice\Helper\Data $helper,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        LayoutInterface $layout,
        ScopeConfigInterface $scopeConfig,
        PageHelper $pageHelper,
        AssetRepository $assetRepository,
        CuracaoHelper $curacaoHelper,
        $blockId
    ) {
        $this->_customerSession = $customerSession;
        $this->_layout = $layout;
        $this->_helper = $helper;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_cart = $cart;
        $this->_priceHelper = $priceHelper;
        $this->_scopeConfig = $scopeConfig;
        $this->_pageHelper = $pageHelper;
        $this->_assetRepository = $assetRepository;
        $this->curacaoHelper = $curacaoHelper;
        $this->_cmsBlock = $this->constructBlock($blockId);
    }

    public function constructBlock($blockId)
    {
        $block = $this->_layout->createBlock('Magento\Cms\Block\Block')
            ->setBlockId($blockId)->toHtml();
        return $block;
    }

    public function getConfig()
    {
        $this->curacaoHelper->updateCuracaoSessionDetails(['down_payment' => 0]);

        $configArr['curacaoPayment']['canApply'] = $this->_canApply;
        $configArr['curacaoPayment']['limit'] = $this->getLimit();
        $configArr['curacaoPayment']['total'] = $this->getDownPayment();
        $configArr['curacaoPayment']['linked'] = $this->_linked;
        $configArr['curacaoPayment']['mediaUrl'] = $this->_assetRepository->getUrl('');
        $configArr['cms_block'] = $this->_cmsBlock;
        $configArr['terms_and_condition'] = $this->checkoutTermsAndConditions();
        $configArr['privacy_link'] = $this->checkoutPrivacyLink();

        return $configArr;
    }

    public function getLimit()
    {
        $curaAccId = $this->getCuracaoId();
        $limit = 0;

        if ($curaAccId) {
            $this->_curacaoId = $curaAccId;
            $this->_canApply = 1;

            $result = $this->_helper->getCreditLimit($curaAccId);

            if ($result) {
                $limit = (float)$result->CREDITLIMIT;
            }

            $formattedCurrencyValue = $this->_priceHelper->currency($limit, true, false);

            return $formattedCurrencyValue;
        }

        return false;
    }

    public function getDownPayment()
    {
        $result = $this->_priceHelper->currency(0, true, false);
        $curaAccId = $this->getCuracaoId();

        if (!$curaAccId) {
            return $result;
        }

        $params = ['cust_id' => $curaAccId, 'amount' => 1];
        $response = $this->_helper->verifyPersonalInfm($params);

        if (!$response) {
            return $result;
        }

        $result = $response->DOWNPAYMENT;
        $this->curacaoHelper->updateCuracaoSessionDetails(['down_payment' => $result]);

        return $this->_priceHelper->currency($result, true, false);
    }

    public function getCuracaoId()
    {
        if (!$this->_customerSession->getCustomerId()) {
            return false;
        }
        $customerId = $this->_customerSession->getCustomerId();
        if ($customerId) {
            /**
             * @var \Magento\Customer\Model\Data\Customer $customer
             * @var \Magento\Framework\Api\AttributeValue|null $curacaoAttribute
             */
            $customer = $this->_customerRepositoryInterface->getById($customerId);
            $curacaoAttribute = $customer->getCustomAttribute('curacaocustid');

            if ($curacaoAttribute) {
                $curaAccId = (string)$customer->getCustomAttribute('curacaocustid')->getValue();

                if ($curaAccId) {
                    $this->curacaoHelper->updateCuracaoSessionDetails(['is_user_linked' => true]);
                    $this->_linked = true;
                    return $curaAccId;
                } else {
                    $this->_linked = false;
                    return false;
                }
            }
        }
        return false;
    }

    /**
     * Provide terms and condition cms page for the checkout page, which is configured in the System > Configuration
     *
     * @return string
     */
    public function checkoutTermsAndConditions()
    {
        $pageId = $this->_scopeConfig->getValue(self::TC_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
        return $this->_pageHelper->getPageUrl($pageId);
    }

    /**
     * Provide privacy link cms page for the checkout page, which is configured in the System > Configuration
     *
     * @return string
     */
    public function checkoutPrivacyLink()
    {
        $pageId = $this->_scopeConfig->getValue(self::PRIVACY_CONFIG_PATH, ScopeInterface::SCOPE_STORE);
        return $this->_pageHelper->getPageUrl($pageId);
    }

}
