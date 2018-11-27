<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       02/08/2018
 */

namespace Dyode\Checkout\Model;

use Dyode\ARWebservice\Exception\ArResponseException;
use Dyode\ARWebservice\Helper\Data as ARWebserviceHelper;
use Dyode\Checkout\Helper\CheckoutConfigHelper;
use Dyode\Checkout\Helper\CuracaoHelper;
use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\View\Asset\Repository as AssetRepository;
use Magento\Framework\View\LayoutInterface;

class ConfigProvider implements ConfigProviderInterface
{

    /**
     * @var \Magento\Framework\View\LayoutInterface
     */
    protected $_layout;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Dyode\ARWebservice\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $_cart;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepository;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * @var \Dyode\Checkout\Helper\CheckoutConfigHelper
     */
    protected $checkoutConfigHelper;

    /**
     * @var \Magento\Checkout\Model\Session
     *
     */
    protected $checkoutSession;

    /**
     * @var bool|int
     */
    protected $_canApply;

    /**
     * Curacao Customer Account Number.
     *
     * @var string|int
     */
    protected $_curacaoId;

    /**
     * Indicate whether user is curacao customer or not.
     *
     * @var bool
     */
    protected $_linked;

    /**
     * Cms Block Html content.
     *
     * @var string
     */
    protected $_cmsBlock;

    /**
     * Holds curacao user credit limit.
     *
     * @var bool
     */
    protected $creditLimit = false;

    /**
     * Holds curacao user down payment.
     *
     * @var bool
     */
    protected $downPayment = 0;

    /**
     * @var bool
     */
    protected $isCreditUsed = false;

    /**
     * @var bool
     */
    protected $canCharge = true;

    /**
     * @var bool|string
     */
    protected $last4digits = false;

    /**
     * ConfigProvider constructor.
     *
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Dyode\ARWebservice\Helper\Data $helper
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Magento\Framework\View\LayoutInterface $layout
     * @param \Magento\Framework\View\Asset\Repository $assetRepository
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     * @param \Dyode\Checkout\Helper\CheckoutConfigHelper $checkoutConfigHelper
     * @param $blockId
     */
    public function __construct(
        PriceHelper $priceHelper,
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        ARWebserviceHelper $helper,
        CustomerRepositoryInterface $customerRepositoryInterface,
        LayoutInterface $layout,
        AssetRepository $assetRepository,
        CuracaoHelper $curacaoHelper,
        CheckoutConfigHelper $checkoutConfigHelper,
        $blockId
    ) {
        $this->_customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->_layout = $layout;
        $this->_helper = $helper;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_priceHelper = $priceHelper;
        $this->_assetRepository = $assetRepository;
        $this->curacaoHelper = $curacaoHelper;
        $this->checkoutConfigHelper = $checkoutConfigHelper;
        $this->_cmsBlock = $this->constructBlock($blockId);
    }

    /**
     * Grab a static block html based on it's id.
     *
     * @param $blockId
     * @return mixed
     */
    public function constructBlock($blockId)
    {
        $block = $this->_layout->createBlock('Magento\Cms\Block\Block')
            ->setBlockId($blockId)->toHtml();

        return $block;
    }

    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        $this->curacaoHelper->updateCuracaoSessionDetails([
            'down_payment'   => $this->downPayment,
            'is_credit_used' => $this->isCreditUsed,
        ]);

        $configArr['curacaoPayment']['canApply'] = $this->_canApply;
        $configArr['curacaoPayment']['last4digits'] = $this->last4digits;
        $configArr['curacaoPayment']['limit'] = $this->getLimit();
        $configArr['curacaoPayment']['total'] = $this->getDownPayment();
        $configArr['curacaoPayment']['totalNaked'] = $this->downPayment;
        $configArr['curacaoPayment']['linked'] = $this->_linked;
        $configArr['curacaoPayment']['canCharge'] = $this->canCharge;
        $configArr['curacaoPayment']['mediaUrl'] = $this->_assetRepository->getUrl('');
        $configArr['cms_block'] = $this->_cmsBlock;
        $configArr['terms_and_condition'] = $this->checkoutConfigHelper->termsAndConditionsLink();
        $configArr['privacy_link'] = $this->checkoutConfigHelper->checkoutPrivacyLink();

        return $configArr;
    }

    /**
     * Collect curacao credit limit.
     *
     * @return bool|float|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getLimit()
    {

        if ($this->creditLimit) {
            return $this->creditLimit;
        }

        $curaAccId = $this->getCuracaoId();
        $limit = 0;

        if ($curaAccId) {
            $this->_curacaoId = $curaAccId;
            $this->_canApply = 1;

            $result = $this->_helper->getCreditLimit($curaAccId);

            if ($result) {
                $limit = (float)$result->CREDITLIMIT;
            }
        }

        $this->creditLimit = $this->_priceHelper->currency($limit, true, false);

        return $this->creditLimit;
    }

    /**
     * Collect curacao down payment info.
     *
     * @return bool|float|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getDownPayment()
    {

        if ($this->downPayment) {
            return $this->_priceHelper->currency($this->downPayment, true, false);
        }

        $downPayment = $this->_priceHelper->currency(0, true, false);
        $curaAccId = $this->getCuracaoId();

        if (!$curaAccId) {
            return $downPayment;
        }

        try {
            $params = ['cust_id' => $curaAccId, 'amount' => $this->collectCuracaoAmountToPass()];
            $response = $this->_helper->verifyPersonalInfm($params);
        } catch (ArResponseException $e) {
            $response = false;
        }

        if (!$response) {
            return $downPayment;
        }

        $result = $response->DOWNPAYMENT;
        $canCharge = (bool)$response->CANCHARGE;
        $isCreditUsed = true;

        if (!$canCharge) {
            $isCreditUsed = false;
        }

        $this->canCharge = $canCharge;
        $this->isCreditUsed = $isCreditUsed;

        $this->curacaoHelper->updateCuracaoSessionDetails([
            'down_payment'   => $result,
            'can_charge'     => $canCharge,
            'is_credit_used' => $isCreditUsed,
        ]);

        $this->downPayment = $result;
        $downPayment = $this->_priceHelper->currency($result, true, false);

        return $downPayment;
    }

    /**
     * Collect curacao customer account number.
     *
     * @return bool|string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCuracaoId()
    {
        if ($this->_curacaoId) {
            return $this->_curacaoId;
        }

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
                    $this->_linked = true;
                    $this->last4digits = substr($curaAccId, -4);
                    $this->curacaoHelper->updateCuracaoSessionDetails([
                        'is_user_linked' => true,
                    ]);

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
     * Collect shipping method delivery messages from system configuration.
     *
     * @return array
     */
    public function collectShippingMethodDeliveryMsgs()
    {
        return $this->checkoutConfigHelper->collectShippingMethodDeliveryMsgs();
    }

    /**
     * Collecting grand total from the quote if present; else pass curacao amount as 1;
     *
     * @return float|int $amount
     */
    protected function collectCuracaoAmountToPass()
    {
        $amount = 1;
        $quote = $this->checkoutSession->getQuote();

        if ($quote) {
            $amount = (float)$quote->getBaseGrandTotal();
        }

        return $amount;
    }

}
