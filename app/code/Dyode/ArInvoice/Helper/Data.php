<?php
/**
 * @package   Dyode
 * @author    Sooraj Sathyan
 */

namespace Dyode\ArInvoice\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
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
        echo $soapResponse->CreateEstimateRevResult;
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
        echo $soapResponse->CreateEstimateRegResult;
        return $soapResponse->CreateEstimateRegResult;
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
     * Post Down Payment to Account/Invoice using API -> WebDownPayment
     */
    public function webDownPayment($custId, $amount, $invNo, $referId)
    {
        $soapClient = $this->setSoapClient();
        $soapResponse = $soapClient->WebDownPayment(
            array(
                'CustID' => $custId,
                'Amount' => $amount,
                'InvNo' => $invNo,
                'ReferID' => $referId
            )
        );
        $response = $soapResponse->WebDownPaymentResult;
        // return $soapResponse;
        var_dump($response);
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
        print_r($response);
        die();
    }
    
}