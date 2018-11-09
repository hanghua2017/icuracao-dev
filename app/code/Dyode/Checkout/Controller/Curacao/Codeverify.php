<?php
/**
 * Dyode_Checkout Magento2 Module.
 *
 *
 * @package Dyode
 * @module  Dyode_Checkout
 * @author  Kavitha <kavitha@dyode.com>
 * @date    12/07/2018
 * @copyright Copyright © Dyode
 */

namespace Dyode\Checkout\Controller\Curacao;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Dyode\Checkout\Helper\CuracaoHelper;
use Magento\Customer\Model\Session;
use Dyode\ARWebservice\Helper\Data;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Codeverify extends Action
{

    /**
     * @param $scrutinize \Dyode\Checkout\Controller\Curacao\Scrutinize
     */
    protected $_scrutinize;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $_resultFactory;

    /**
     * @var \Dyode\Checkout\Helper\CuracaoHelper
     */
    protected $_curacaoHelper;

    /**
     * @param $customerSession \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var Dyode\ARWebservice\Helper\Data
     */
    protected $helper;

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
     * Codeverify constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\ResultFactory $resultFactory
     * @param \Dyode\Checkout\Controller\Curacao\Scrutinize $scrutinize
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Dyode\Checkout\Helper\CuracaoHelper $curacaoHelper
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Dyode\ARWebservice\Helper\Data $helper
     */
    public function __construct(
        Context $context,
        ResultFactory $resultFactory,
        Scrutinize $scrutinize,
        Session $customerSession,
        CuracaoHelper $curacaoHelper,
        PriceHelper $priceHelper,
        Data $helper
    ) {
        parent::__construct($context);
        $this->_resultFactory = $resultFactory;
        $this->_scrutinize = $scrutinize;
        $this->_curacaoHelper = $curacaoHelper;
        $this->_customerSession = $customerSession;
        $this->helper = $helper;
        $this->priceHelper = $priceHelper;
    }

    public function execute()
    {
        $postVariables = (array)$this->getRequest()->getPost();

        if (!empty($postVariables)) {
            try {
                $this->validateCode();
                $this->_scrutinize->linkUser();
                $this->_scrutinize->collectUserCreditLimit();
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
    public function validateCode()
    {
        /** @var \Magento\Framework\DataObject $curacaoInfo */
        $curacaoInfo = $this->_curacaoHelper->getCuracaoSessionInformation();

        $verificationCode = $this->getRequest()->getParam('verify_code', false);
        $encodeCode = $this->_curacaoHelper->getCuracaoSessionInformation()->getEncCode();
        $this->_curacaoHelper->getCuracaoSessionInformation()->unsEncCode();
        $amount = $this->_scrutinize->collectCuracaoAmountToPass();
        $postData = [
            'cust_id' => $curacaoInfo->getAccountNumber(),
            'amount'  => $amount, //this field is mandatory and hence put a sample value;
        ];

        // Check if code is good 0 good -1 no good
        $checkResult = $this->helper->verifyCode($encodeCode, $verificationCode);

        //If Result is verified then link the Curacao account with Magento
        if ($checkResult != 0) {
            $this->downPayment = false;
            $this->_curacaoHelper->updateCuracaoSessionDetails(['is_user_linked' => false]);
            throw new \Exception('Code verification failed');
        } else {

            //send api call to collect user info.
            $verifyResult = $this->helper->verifyPersonalInfm($postData);

            if ($verifyResult == false) {
                $this->downPayment = false;
                $this->_curacaoHelper->updateCuracaoSessionDetails(['is_user_linked' => false]);
                throw new \Exception('Api failed');
            } else {
                $downPayment = (float)$verifyResult->DOWNPAYMENT;
                $this->_curacaoHelper->updateCuracaoSessionDetails(['down_payment' => $downPayment]);
                $this->downPayment = $downPayment;
            }
        }

        return $this;
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
        $output['curacaoInfo']['creditLimit'] = $this->_scrutinize->getCreditLimit();
        $output['curacaoInfo']['downPaymentNaked'] = $this->downPayment;
        $output['curacaoInfo']['downPayment'] = $this->priceHelper->currency($this->downPayment, true, false);

        return $output;
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

}
