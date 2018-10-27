<?php
/**
 * Dyode_ARWebservice Magento2 Module.
 *
 *
 * @package   Dyode
 * @module    Dyode_ARWebservice
 * @author    Kavitha <kavitha@dyode.com>
 * @date      03/07/2018
 * @copyright Copyright © Dyode
 */

namespace Dyode\ARWebservice\Helper;

use Magento\Framework\Message\ManagerInterface as MessageManager;
use Dyode\AuditLog\Model\ResourceModel\AuditLog;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Zend\Http\Client;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     *
     * @var type \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     *
     * @var type \Magento\Framework\Message\ManagerInterface
     */
    protected $auditLog;

    /**
     *
     * @var type \Zend\Http\Client
     */
    protected $zendClient;

    /**
     * Data constructor.
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     * @param \Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog
     * @param \Zend\Http\Client $zendClient
     */
    public function __construct(
        Context $context,
        MessageManager $messageManager,
        AuditLog $auditLog,
        Client $zendClient
    ) {
        $this->messageManager = $messageManager;
        $this->auditLog = $auditLog;
        $this->zendClient = $zendClient;
        parent::__construct($context);
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * To retrieve the API user in system configuration
     *
     * @return string
     */
    public function getApiUser()
    {
        return $this->getConfig('linkaccount/curacao/apiuser');
    }

    /**
     * To retrieve the API Password in system configuration
     *
     * @return string
     */
    public function getApiPass()
    {
        return $this->getConfig('linkaccount/curacao/apipass');
    }

    /**
     * To retrieve the WSDL Url in system configuration
     *
     * @return string
     */
    public function getWsdlUrl()
    {
        return $this->getConfig('linkaccount/curacao/wsdlurl');
    }

    /**
     * To retrieve the API Url in system configuration
     *
     * @return string
     */
    public function getApiUrl()
    {
        return $this->getConfig('linkaccount/curacao/apiurl');
    }

    /**
     * To retrieve the REST API Key in system configuration
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->getConfig('linkaccount/curacao/apikey');
    }

    /**
     * Function to connect the AR using REST
     *
     * @param string $fnName Function namespace
     * @param $params Parameters to pass to AR REST Service
     * @return bool|\Zend\Http\Response
     */
    public function arConnect($fnName, $params)
    {
        $apiUrl = $this->getApiUrl();
        $apiKey = $this->getApiKey();
        $curlUrl = $apiUrl . $fnName;

        try {
            $this->zendClient->reset();
            $this->zendClient->setUri($curlUrl);
            $this->zendClient->setMethod(\Zend\Http\Request::METHOD_GET);
            $this->zendClient->setParameterGet($params);
            $this->zendClient->setHeaders([
                'Content-Type' => 'application/json',
                'Accept'       => 'application/json',
                'X-Api-Key'    => $apiKey,
            ]);

            $this->zendClient->send();
            return $this->zendClient->getResponse();

        } catch (\Zend\Http\Exception\RuntimeException $runtimeException) {
            $this->auditLog->saveAuditLog([
                'user_id'     => "",
                'action'      => 'AR webservice',
                'description' => "Fail to connect AR webservice",
                'client_ip'   => "",
                'module_name' => "Dyode_ARWebservice",
            ]);
            $this->messageManager->addError(__('Please try after some time '));
            return false;
        }
    }

    /**
     * Get customer information from AR by using customer ID
     *
     * @param  string $cu_account
     * @return \stdClass|bool
     */
    public function getARCustomerInfoAction($cu_account)
    {

        /**
         * @var \Zend\Http\Response|bool $restResponse
         * @var \stdClass $result
         */

        if (!isset($cu_account) || !is_numeric($cu_account)) {
            return false;
        }

        //sending api request
        $params = ['cust_id' => $cu_account];
        $restResponse = $this->arConnect('GetCustomerContact', $params);

        if (!$restResponse) {
            return false;
        }

        $result = json_decode($restResponse->getBody());

        if ($result->OK != true) {

            //logging audit log
            $this->auditLog->saveAuditLog([
                'user_id'     => "",
                'action'      => 'Get AR Customer Contact',
                'description' => "Fail to get customer contact",
                'client_ip'   => "",
                'module_name' => "Dyode_ARWebservice",
            ]);
            return false;
        }

        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id'     => "",
            'action'      => 'Get AR Customer Contact',
            'description' => "Obtained Customer Contact for id " . $cu_account,
            'client_ip'   => "",
            'module_name' => "Dyode_ARWebservice",
        ]);

        $custInfo = $result->DATA;
        return $custInfo;

    }

    /**
     * Validate Customer Information and get DownPayment
     *
     * @param array $customerDetails
     * @return bool
     */
    public function verifyPersonalInfm(array $customerDetails)
    {
        $restResponse = $this->arConnect('ValidateDP', $customerDetails);

        if (!$restResponse) {
            return false;
        }

        $result = json_decode($restResponse->getBody());

        if ($result->OK != true) {

            //logging audit log
            $this->auditLog->saveAuditLog([
                'user_id'     => "",
                'action'      => 'AR Customer Details Verification',
                'description' => "Fail to Verify Customer Details",
                'client_ip'   => "",
                'module_name' => "Dyode_ARWebservice",
            ]);
            return false;
        }

        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id'     => "",
            'action'      => 'AR Customer Details Verification',
            'description' => "AR Customer details verification success",
            'client_ip'   => "",
            'module_name' => "Dyode_ARWebservice",
        ]);

        $verifiedResult = $result->DATA;
        return $verifiedResult;

    }


    /**
     * Function to send the verification code
     *
     * $type 0 -> Send code as text; 1-> Send code as Voice
     *
     * @param $_phonenumber
     * @param $_times
     * @param $_type
     * @return int|string
     */
    public function phoneVerifyCode($_phonenumber, $_times, $_type)
    {
        $salt = 'ag#A\J9.u=j^v}X3';
        $code = rand(10000, 99999);
        $_phonenumber = '(832)977-1260';
        $params = array();

        $wsdlUrl = $this->getConfig('linkaccount/curacao/phonewsdlurl');
        $client = new \SoapClient($wsdlUrl,array( "trace" => 1 ));
        $licenseKey = $this->getConfig('linkaccount/curacao/licensekey');
        $callerID = $this->getConfig('linkaccount/curacao/callerid');
        $countryCode = '1';
        $valuesToDelete = ['(', ')', '-', ' '];
        $phone = str_replace($valuesToDelete, '', $_phonenumber);
        $params['PhoneNumber'] = $phone;
        $params['LicenseKey'] = $licenseKey;
        
        if ($_type == 1) {  
           
            $params['CountryCode'] = '1';           
            $params['CallerID'] = $callerID;
            $params['Language'] = 'en';
            $params['VerificationCode'] = $code;
            $params['Extension'] = '';
            $params['ExtensionPauseTime'] = '';

            $response = $soapClient->PlaceCall($params);
            $result= $response->PlaceCallResult;
            if (isset($result->Error)) {
                    return -1;
            }
        } else {
          
            $message = 'Your Curacao verification code is ' . $code . '.';
            $response = $soapClient->SendSMS($params);
            $result = $response->SendSMSResult;
            if (isset($result->Error)) {
                    return -1;
            }
        }
        return trim(md5($salt . $code));
    }

    /**
     * Verify code return 0 is verified
     *
     * @param $_enc
     * @param $_vid
     * @return int
     */
    public function verifyCode($_enc, $_vid)
    {
        $salt = 'ag#A\J9.u=j^v}X3';
        if (trim($_enc) === trim(md5($salt . $_vid))) {
            return 0;
        } else {
            return -1;
        }
    }

    /**
     * Function to return the credit limit
     *
     * @param array $cu_account
     * @return bool
     */
    public function getCreditLimit($cu_account)
    {
        if (!isset($cu_account) || !is_numeric($cu_account)) {
            return false;
        }

        $params = ['cust_id' => $cu_account];
        $restResponse = $this->arConnect('GetCustomerCreditLimit', $params);

        if (!$restResponse) {
            return false;
        }

        $result = json_decode($restResponse->getBody());

        if ($result->OK != 1) {

            //logging audit log
            $this->auditLog->saveAuditLog([
                'user_id'     => "",
                'action'      => 'AR Customer Credit Limit',
                'description' => "AR Customer Credit Limit Failed",
                'client_ip'   => "",
                'module_name' => "Dyode_ARWebservice",
            ]);
            return false;
        }

        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id'     => "",
            'action'      => 'AR Customer Credit Limit',
            'description' => "AR Customer Credit Limit success",
            'client_ip'   => "",
            'module_name' => "Dyode_ARWebservice",
        ]);
        return $result->DATA;
    }

}
