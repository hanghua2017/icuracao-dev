<?php
/**
 * ArInoice Helper
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan
 */

namespace Dyode\ArInvoice\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Initialize Rest Api Connection
     */
    public function initRestApiConnect($url)
    {
        /*
        * Init Curl
        */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /*
        * Set Content Header
        */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Api-Key: TEST-WNNxLUjBxA78J7s',
            'Content-Type: application/json',
            )
        );
        return $ch;
    }
    /**
     * Create Invoice using API -> CreateRevEstimate
     */
    public function createRevInvoice($inputArray)
    {
        /*
        * Initialize Rest Api Connection
        */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/CreateRevEstimate";
        $ch = $this->initRestApiConnect($url);
        /*
        * Set Post Data
        */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inputArray));
        /**
         * Get Response Data
         */
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
    /**
     * Post Down Payment to Account/Invoice using API -> WebDownPayment
     */
    public function webDownPayment($custId, $amount, $invNo, $referId)
    {
        # Input Array
        $inputArray = array(
            "cust_id" => $custId,
            "amount" => $amount,
            "inv_no" => $invNo,
            "referID" => $referId
        );
        /*
        * Initialize Rest Api Connection
        */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/webDownpayment";
        $ch = $this->initRestApiConnect($url);
        /*
        * Set Post Data
        */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inputArray));
        /**
         * Get Response Data
         */
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }
    /**
     * Supply Invoice using API -> SupplyInvoice
     */
    public function supplyInvoice($invNo, $firstName, $lastName, $email)
    {
        # Input Array
        $inputArray = array(
            "InvNo" => $invNo,
            "FirstName" => $firstName,
            "LastName" => $lastName,
            "eMail" => $email
        );
        /*
        * Initialize Rest Api Connection
        */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/SupplyInvoice";
        $ch = $this->initRestApiConnect($url);
        /*
        * Set Post Data
        */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($inputArray));
        /**
         * Get Response Data
         */
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }
    /**
     * Validate Account Number
     */
    public function validateAccountNumber($accountNumber)
    {
        // dummy content
        // $accountNumber = "52041";
        if (strlen($accountNumber) == 7) {
            return $accountNumberFormatted = substr_replace($accountNumber, "-", 3, 0);
        }
        elseif (strlen($accountNumber) < 7) {
            $accountNumber = str_pad($accountNumber, 7, "0", STR_PAD_LEFT);
            return $accountNumberFormatted = substr_replace($accountNumber, "-", 3, 0);
        }
        else {
            return $accountNumberFormatted = $accountNumber;
        }
    }
    /**
     * Check if Customer is Active using API -> isCustomerActive
     */
    public function isCustomerActive($customerId)
    {
      $soapClient = $this->setSoapClient();
      $soapResponse = $soapClient->IsCustomerActive(array('CustomerID' => $customerId));
      $returnValue = explode(";",$soapResponse->IsCustomerActiveResult);
      return $returnValue[0];
    }

    /**
     * Validate shipping address with default customer address from AR
     */
    public function validateAddress($customerId, $shippingStreet, $shippingZip)
    {
        //Get Customer Address from AR
        $addressMismatch = null;
        $defaultCustomerAddress = $this->getCustomerContact($customerId);
        if (!empty($defaultCustomerAddress)) {
            $defaultZip = substr($defaultCustomerAddress->ZIP, 0, 5);
            $defaultStreet = $defaultCustomerAddress->STREET;
            if($shippingStreet == $defaultStreet && $shippingZip == $defaultZip){
                //Set Address Mismatch flag
                return $addressMismatch = False;
            } else {
                //Set Address Mismatch flag
                return $addressMismatch = True;
            }
        }
        else {
            return $addressMismatch = True;   
        }
    }
    /**
     * Get Customer Contact Address using API -> getCustomerContact
     */
    public function getCustomerContact($customerId)
    {
        $soapClient = $this->setSoapClient();
        $soapResponse = $soapClient->GetCustomerContact(array('cust_id' => $customerId));
        $response = json_decode($soapResponse->GetCustomerContactResult);
        $customerInfo = json_decode(json_encode($response->DATA));
        return $customerInfo;
    }
    /**
     * Setting up the Soap Client
     */
    public function setSoapClient()
    {
        $wsdlUrl = 'https://exchangeweb.lacuracao.com:2007/ws1/test/ecommerce/Main.asmx?WSDL';
        $soapClient = new \SoapClient($wsdlUrl,['version' => SOAP_1_2]);
        $xmlns = 'http://lacuracao.com/WebServices/eCommerce/';
        $headerbody = array('UserName' => 'mike',
            'Password' => 'ecom12');
        //Create Soap Header.
        $header = new \SOAPHeader($xmlns, 'TAuthHeader', $headerbody);
        //Setting the Headers of Soap Client.
        $soapHeader = $soapClient->__setSoapHeaders($header);
        return $soapClient;
    }
    /**
     * Get Customer Contact Address using API -> geCustomerContact
     */
    public function goSupplyInvoice($invNo, $firstName, $lastName, $email)
    {
        $soapClient = $this->setSoapClient();
        $soapResponse = $soapClient->GoSupplyInvoice(
            array(
                'InvNo' => $invNo,
                'FirstName' => $firstName,
                'LastName' => $lastName,
                'eMail' => $email
            )
        );
        $response = json_decode($soapResponse->GoSupplyInvoiceResult);
        // return $soapResponse;
        // var_dump($response);
        die();
    }
}