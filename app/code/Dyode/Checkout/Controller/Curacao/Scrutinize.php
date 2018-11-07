<?php
/**
 * Dyode_ARWebservice Magento2 Module.
 *
 *
 * @package Dyode
 * @module  Dyode_ARWebservice
 * @author  Kavitha <kavitha@dyode.com>
 * @date    12/07/2018
 * @copyright Copyright © Dyode
 */

namespace Dyode\Checkout\Controller\Curacao;

use Dyode\ARWebservice\Exception\ArResponseException;
use Dyode\ARWebservice\Helper\Data as ARWebserviceHelper;
use Dyode\Checkout\Helper\CuracaoHelper;
use Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Encryption\EncryptorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Store\Model\StoreManagerInterface;
use Zend\Serializer\Adapter\Json;

class Scrutinize extends Action
{
    /**
     * @var \Dyode\ARWebservice\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $_resultFactory;

    /**
     * @var \stdClass
     */
    protected $verifyResult;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Api\Data\CustomerInterface
     */
    protected $customerData;

    /**
     * @var \Magento\Framework\Encryption\EncryptorInterface
     */
    protected $encryptor;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customer;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Magento\Quote\Model\QuoteIdMaskFactory
     */
    protected $quoteIdMaskFactory;

    /**
     * @var boolean|string
     */
    protected $downPayment;

    /**
     * @var string
     */
    protected $creditLimit;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $curacaoHelper;

    /**
     * @var \Zend\Serializer\Adapter\Json
     */
    protected $jsonHelper;

    /**
     * @var string
     */
    protected $curacaoIdAttribute = 'curacaocustid';

    /**
     * Scrutinize constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Dyode\ARWebservice\Helper\Data $helper
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customerRepository
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Magento\Customer\Api\Data\CustomerInterface $customerData
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     * @param \Zend\Serializer\Adapter\Json $jsonHelper
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        StoreManagerInterface $storeManager,
        ARWebserviceHelper $helper,
        CustomerSession $customerSession,
        CustomerRepository $customerRepository,
        CartRepositoryInterface $quoteRepository,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CustomerInterface $customerData,
        EncryptorInterface $encryptor,
        PriceHelper $priceHelper,
        CuracaoHelper $curacaoHelper,
        Json $jsonHelper
    ) {
        $this->_resultFactory = $resultFactory;
        $this->storeManager = $storeManager;
        $this->_helper = $helper;
        $this->_customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->quoteRepository = $quoteRepository;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->customerData = $customerData;
        $this->encryptor = $encryptor;
        $this->priceHelper = $priceHelper;
        $this->curacaoHelper = $curacaoHelper;
        $this->jsonHelper = $jsonHelper;

        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $postVariables = (array)$this->getRequest()->getPost();

        if (!empty($postVariables)) {
            try {
                $this->validateUserInformation();
                $this->linkUser();
                $this->collectUserCreditLimit();
                return $this->successResponse();
            } catch (ArResponseException $e) {
                $result = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
                $result->setData([
                    'message' => $e->getMessage(),
                    'type'    => 'error',
                ]);

                return $result;

            } catch (\Exception $exception) {
                return $this->verificationFailedResponse();
            }
        }

        return $this->verificationFailedResponse();
    }

    /**
     * Send ARWebservice API request in order to verify user information.
     *
     * @return $this
     * @throws \Exception
     */
    public function validateUserInformation()
    {
        /** @var \Magento\Framework\DataObject $curacaoInfo */
        $curacaoInfo = $this->curacaoHelper->getCuracaoSessionInformation();
        $ssnLast = $this->getRequest()->getParam('ssn_last', false);
        $zipCode = $this->getRequest()->getParam('zip_code', false);
        $maidenName = $this->getRequest()->getParam('maiden_name', false);
        $dob = $this->getRequest()->getParam('date_of_birth', false);
        $amount = $this->collectCuracaoAmountToPass();
        $postData = [
            'cust_id' => $curacaoInfo->getAccountNumber(),
            'amount'  => $amount, //this field is mandatory and hence put a sample value;
        ];

        if ($ssnLast) {
            $postData['ssn'] = $ssnLast;
        }
        if ($zipCode) {
            $postData['zip'] = $zipCode;
        }
        if ($maidenName) {
            $postData['mmaiden'] = $maidenName;
        }
        if ($dob) {
            $postData['dob'] = date("Y-m-d", strtotime ($dob) );
        }

        //send api call to collect user info.
        $verifyResult = $this->_helper->verifyPersonalInfm($postData);

        if ($verifyResult == false) {
            $this->downPayment = false;
            $this->curacaoHelper->updateCuracaoSessionDetails(['is_user_linked' => false]);
            throw new \Exception('Api failed');
        } else {
            $downPayment = (float)$verifyResult->DOWNPAYMENT;
            $this->curacaoHelper->updateCuracaoSessionDetails(['down_payment' => $downPayment]);
            $this->downPayment = $downPayment;
        }

        return $this;
    }

    /**
     * Link a curacao identification number to a customer.
     *
     * Here a customer account will be created if the user does not exist in the website.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\State\InputMismatchException
     */
    public function linkUser()
    {
        /** @var \Magento\Framework\DataObject $curacaoInfo */
        $curacaoInfo = $this->curacaoHelper->getCuracaoSessionInformation();

        if ($this->_customerSession->isLoggedIn()) {
            //linking curacao id to the logged in customer.
            $customerId = (int)$this->_customerSession->getCustomerId();
            $this->customerData->setId($customerId)
                ->setCustomAttribute($this->curacaoIdAttribute, $curacaoInfo->getAccountNumber());
            $this->customer = $this->customerRepository->save($this->customerData);
        } else {
            $store = $this->storeManager->getStore();
            $hashPassword = $this->encryptor->encrypt($curacaoInfo->getPassword());
            try {
                //this scenario should not occur.
                if (!$curacaoInfo->getEmailAddress()) {
                    throw new NoSuchEntityException(__('Customer email address cannot be empty'));
                }

                //checks whether a customer with the given email address does exists in the website.
                $customerData = $this->customerRepository->get($curacaoInfo->getEmailAddress());
                $customerData->setCustomAttribute($this->curacaoIdAttribute, $curacaoInfo->getAccountNumber());

                //linking curacao id with the existing customer.
                $this->customer = $this->customerRepository->save($customerData);
            } catch (NoSuchEntityException $exception) {
                //Confirmed customer is a guest; so let's create a new customer account for the user.
                $this->customerData->setEmail($curacaoInfo->getEmailAddress())
                    ->setFirstname($curacaoInfo->getFirstName())
                    ->setLastname($curacaoInfo->getLastName())
                    ->setStoreId($store->getStoreId())
                    ->setWebsiteId($this->storeManager->getStore()->getWebsiteId())
                    ->setCustomAttribute($this->curacaoIdAttribute, $curacaoInfo->getAccountNumber());

                //linking curacao id with the newly created customer.
                $this->customer = $this->customerRepository->save($this->customerData, $hashPassword);
            }
        }

        $this->_customerSession->setCuracaoCustomerId($this->customer->getId());
        $this->curacaoHelper->updateCuracaoSessionDetails(['is_user_linked' => true, 'is_credit_used' => true]);
        return $this;
    }

    /**
     * Collect curacao user's credit limit.
     *
     * @return $this
     */
    public function collectUserCreditLimit()
    {
        /** @var \Magento\Framework\DataObject $curacaoInfo */
        $curacaoInfo = $this->curacaoHelper->getCuracaoSessionInformation();

        $creditLimitInfo = $this->_helper->getCreditLimit($curacaoInfo->getAccountNumber());
        $this->creditLimit = $this->priceHelper->currency(0.00, true, false);

        if ($creditLimitInfo) {
            $this->creditLimit = $this->priceHelper->currency((float)$creditLimitInfo->CREDITLIMIT, true, false);
        }

        return $this;
    }

    /**
     * Function to return the  credit Limit
     */
    public function getCreditLimit(){
        return $this->creditLimit;
    }
    /**
     * Fail response.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function verificationFailedResponse()
    {
        $result = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData([
            'message' => __('Curacao account verification failed. Please try later.'),
            'type'    => 'error',
        ]);

        return $result;
    }

    /**
     * Success response.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    protected function successResponse()
    {
        $output = $this->prepareResponse();
        $result = $this->_resultFactory->create(ResultFactory::TYPE_JSON);
        $result->setData([
            'data' => $output,
            'type' => 'success',
        ]);
        return $result;
    }

    /**
     * Response data.
     *
     * Curacao account information and revamped quote totals will be prepared as data response.
     *
     * @return array $output
     */
    protected function prepareResponse()
    {
        $output['curacaoInfo']['creditLimit'] = $this->creditLimit;
        $output['curacaoInfo']['downPaymentNaked'] = $this->downPayment;
        $output['curacaoInfo']['downPayment'] = $this->priceHelper->currency($this->downPayment, true, false);

        return $output;
    }

    /**
     * Prepare curacao amount to be passed.
     *
     * Curacao Amount  = Current Base Grand Total + Aggregated Shipping Amount Calculated from Quote Items.
     *
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function collectCuracaoAmountToPass()
    {
        $curacaoAmount = 1;
        $shippingAmount = 0;
        $cartId = $this->getRequest()->getParam('quote_id', false);

        //if no quote_id in the request, then we don't want to proceed.
        if (!$cartId) {
            return $curacaoAmount;
        }

        //load active quote model
        if (!$this->_customerSession->isLoggedIn()) {
            $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
            $cartId = $quoteIdMask->getQuoteId();
        }
        $quote = $this->quoteRepository->getActive($cartId);

        //calculate total shipping amount by looping through the quote items.
        foreach ($quote->getItems() as $quoteItem) {
            if ($quoteItem->getProductType() === 'virtual'
                || $quoteItem->getDeliveryType() != DeliveryMethod::DELIVERY_OPTION_SHIP_TO_HOME_ID
            ) {
                continue;
            }

            $shipmentData = $this->jsonHelper->unserialize($quoteItem->getShippingDetails());

            if (isset($shipmentData['amount'])) {
                $shippingAmount += $shipmentData['amount'];
            }
        }

        $curacaoAmount = $quote->getBaseGrandTotal() + $shippingAmount;

        return $curacaoAmount;
    }
}
