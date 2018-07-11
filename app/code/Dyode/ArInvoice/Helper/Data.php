<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */

namespace Dyode\ArInvoice\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * To check the account number is required format
     */
    public function validateAccountNumber($accountNumber)
    {
        // dummy content
        $accountNumber = "52041";
        if (strlen($accountNumber) == 7) {
            $accountNumberFormatted = substr_replace($accountNumber, "-", 3, 0);
        }
        elseif (strlen($accountNumber) < 7) {
            $accountNumber = str_pad($accountNumber, 7, "0", STR_PAD_LEFT);
            $accountNumberFormatted = substr_replace($accountNumber, "-", 3, 0);
        }
        else {
            $accountNumberFormatted = $accountNumber;   
        }

        // Regular expression for Account Numbers
        $regex = "/^[0-9]{3}-[0-9]{4}$/";

        if (!preg_match($regex, $accountNumberFormatted)) {
            $soapClient = $this->setSoapClient();
            $customerStatus = $this->checkCustomerStatus($accountNumberFormatted);
            #incomplete ...
        }
        else {
            return $accountNumberFormatted; //Formatted Account Number
        }
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
     * Create Invoice using API -> CreateEstimateRev
     */
    public function createInvoiceRev($inputArray)
    {
        $soapClient = $this->setSoapClient();
        $soapResponse = $soapClient->CreateEstimateRev($inputArray);
        // echo $soapResponse->CreateEstimateRevResult;
        // var_dump($soapResponse);
        return $soapResponse->CreateEstimateRevResult;
    }

    /**
     * Create Invoice using API -> CreateEstimateReg
     */
    public function createInvoiceReg($inputArray)
    {
        $soapClient = $this->setSoapClient();
        $soapResponse = $soapClient->CreateEstimateReg($inputArray);
        // var_dump($soapResponse);
        // echo $soapResponse->CreateEstimateRegResult;
        return $soapResponse->CreateEstimateRegResult;
    }
}

 

        