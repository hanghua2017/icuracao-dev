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
            $this->arErrorLogs("GetCustomerContact", "Failed to connect AR webservice");

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

        $this->arErrorLogs("GetCustomerContact", "Parameters to AR webservice" . $cu_account);

        $restResponse = $this->arConnect('GetCustomerContact', $params);

        if (!$restResponse) {
            $this->arErrorLogs("GetCustomerContact", "Not getting response from AR webservice");

            return false;
        }

        $result = json_decode($restResponse->getBody());

        if ($result->OK != true) {
            $this->arErrorLogs("GetCustomerContact",
                "Failed to get customer contact details " . $restResponse->getBody());

            return false;
        }
        $this->arErrorLogs("GetCustomerContact", "Obtained Customer Details for id "  . $restResponse->getBody());

        $custInfo = $result->DATA;
        return $custInfo;

    }

    /**
     * Validate Customer Information and get DownPayment
     *
     * @param array $customerDetails
     * @return bool
     * @throws \Dyode\ARWebservice\Exception\ArResponseException
     */
    public function verifyPersonalInfm(array $customerDetails)
    {
        $restResponse = $this->arConnect('ValidateDP', $customerDetails);

        $this->arErrorLogs("ValidateDP", "Parameters to AR webservice" . json_encode($customerDetails));

        if (!$restResponse) {
            $this->arErrorLogs("ValidateDP", "Response is null");
            return false;
        }

        $result = json_decode($restResponse->getBody());

        if ($result->OK != true) {
            $this->arErrorLogs("ValidateDP", "Failed to Verify Customer Details" . $restResponse->getBody());

            $error = $this->getErrorCodes($result->INFO);

            return false;
        }

        $this->arErrorLogs("ValidateDP", "Verification success" . $restResponse->getBody());

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

       // $_phonenumber  = '(832)977-1260';

        $licenseKey = $this->getConfig('linkaccount/curacao/licensekey');
        $callerID = $this->getConfig('linkaccount/curacao/callerid');
        $wsdlUrl = $this->getConfig('linkaccount/curacao/phonewsdlurl');

        $countryCode = '1';
        $valuesToDelete = ['(', ')', '-', ' '];
        $phoneNumber = str_replace($valuesToDelete, '', $_phonenumber);
        $extension = '';
        $extensionPauseTime = '';

        if ($_type == 1) {

            $params = [
                'CountryCode'        => $countryCode,
                'PhoneNumber'        => $phoneNumber,
                'LicenseKey'         => $licenseKey,
                'Language'           => 'en',
                'VerificationCode'   => $code,
                'Extension'          => '',
                'ExtensionPauseTime' => '',
            ];

            // $URL = $wsdlUrl . "PlaceCall?CountryCode=" . $countryCode . "&PhoneNumber=" . $phoneNumber. "&VerificationCode=" . $verifyCode . "&CallerID=" . $callerID . "&Language=" . $language . "&LicenseKey=" . $licenseKey;

            // Get cURL resource
            // $curl = curl_init();
            // curl_setopt_array($curl, [
            //     CURLOPT_RETURNTRANSFER => 1,
            //     CURLOPT_URL            => $URL,
            //     CURLOPT_USERAGENT      => 'Service Objects Telephone Verification',
            // ]);
            // curl_setopt($curl, CURLOPT_TIMEOUT, 50); //timeout in seconds
            // // Send the request & save response to $resp
            // $resp = curl_exec($curl);

            // if ($resp == false) {
            //     curl_close($curl);
            //     return -1;
            // }

            $soapClient = new \SoapClient($wsdlUrl . "?WSDL/", ["trace" => 1]);
            $response = $soapClient->__soapCall("PlaceCall", $params);
            $this->arErrorLogs("SMS", "Successfully placed call to " . $phoneNumber);    

            // $response = $soapClient->PlaceCall($params);
            // $result= $response->PlaceCallResult;
            if (isset($result->Error)) {
                $this->arErrorLogs("Call", "Failed to place a call to " . $phoneNumber);
                return -1;
            }

           

        } else {

            $message = 'Your Curacao verification code is ' . $code . '.';

            $params = [
                'CountryCode' => $countryCode,
                'PhoneNumber' => $phoneNumber,
                'Message'     => $message,
                'LicenseKey'  => $licenseKey,
            ];

            try {
                $this->zendClient->reset();
                $this->zendClient->setUri($wsdlUrl . "/SendSMS");
                $this->zendClient->setMethod(\Zend\Http\Request::METHOD_POST);
                $this->zendClient->setParameterPost($params);
                $this->zendClient->send();
                $this->arErrorLogs("SMS", "Successfully send SMS to " . $phoneNumber);
                return trim(md5($salt . $code));

            } catch (\Zend\Http\Exception\RuntimeException $runtimeException) {

                $this->arErrorLogs("SMS", "Fail to send SMS to " . $phoneNumber);
                return -1;
            }

        }

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
            $this->arErrorLogs("AR Customer Credit Limit",
                "AR Customer Credit Limit Failed" . $restResponse->getBody());

            return false;
        }

        $this->arErrorLogs("AR Customer Credit Limit", "AR Customer Credit Limit success" . $restResponse->getBody());

        return $result->DATA;
    }

    /**
     * Function to send the AR message
     *
     * @param String
     * @param String
     */

    public function arErrorLogs($action, $description)
    {
        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id'     => "",
            'action'      => $action,
            'description' => $description,
            'client_ip'   => "",
            'module_name' => "Dyode_ARWebservice",
        ]);
    }

    /**
     * Function to get the error codes
     *
     * @param $codeInfo
     * @throws \Dyode\ARWebservice\Exception\ArResponseException
     */
    public function getErrorCodes($codeInfo)
    {
        $errorArr = explode("[", $codeInfo);

        if ($errorArr[0] == 'Authentication error') {
            $errorCodes = explode(" ", trim(str_replace("]", "", trim($errorArr[1]))));

            $errorMsg = 'Following fields are invalid: ';
            $counter = 0;
            foreach ($errorCodes as $code) {
                if ($code != "") {
                    switch ($code) {
                        case 'SSN':
                            $errorMsg .= 'SSN';
                            break;
                        case 'DOB':
                            $errorMsg .= 'DOB';
                            break;
                        case 'MAIDEN':
                            $errorMsg .= 'Mothers Maiden Name';
                            break;
                        case 'ZIP':
                            $errorMsg .= 'Zipcode';
                            break;
                    }
                    if ($counter < (count($errorCodes) - 1)) {
                        $errorMsg .= ', ';
                    }
                }
                $counter++;
            }

            throw new \Dyode\ARWebservice\Exception\ArResponseException(__($errorMsg));

        }
    }
}
