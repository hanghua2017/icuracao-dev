<?php
/**
 * Dyode_CheckoutAddressStep Magento2 Module.
 *
 *
 * @package   Dyode
 * @module    Dyode_CheckoutAddressStep
 * @author    Kavitha <kavitha@dyode.com>
 * @date      24/10/2018
 * @copyright Copyright © Dyode
 */

 namespace Dyode\CheckoutAddressStep\Helper;

use Magento\Framework\App\Helper\Context;
use Dyode\StoreLocator\Model\GeoCoordinateRepository;

class Data extends \Magento\Framework\App\Helper\AbstractHelper{
    /**
     * @var type \Magento\UPS\Model\Carrier
     */
    protected $uspsUrl;
    protected $uspsId;
    protected $uspsSize;
    protected $uspsContainer;
    protected $zipcodefrom;
    protected $stampsUrl;
    protected $stampUser;
    protected $stampPass;
    protected $integrationId;
    protected $defaultCost;

    /**
     * Xml access request
     *
     * @var string
     */
    protected $_xmlAccessRequest;
    /**
     * Xml writer
     *
     * @var string
     */
    protected $_xmlWriter;
   
     /**
     * @var \Dyode\StoreLocator\Model\GeoCoordinateRepository
     */
    protected $geoCoordinateRepository;

    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * 
     */   
    public function __construct(
        Context $context,
        GeoCoordinateRepository $geoCoordinateRepository       
    ){
        parent::__construct($context);
        $this->zipcodefrom = $this->getZipcodeFrom();
        $this->geoCoordinateRepository = $geoCoordinateRepository;
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
     /**
     * Function to return the admin configuration
     */
    protected function getUSPSUrl(){
        return $this->getConfig('shippingsettings/curacao_shipping/uspsurl');
    }

    protected function getUSPSId(){
        return $this->getConfig('shippingsettings/curacao_shipping/uspsuserid');
    }

    protected function getUSPSSize(){
        return $this->getConfig('shippingsettings/curacao_shipping/uspssize');
    }

    protected function getUSPSContainer(){
        return $this->getConfig('shippingsettings/curacao_shipping/uspscontainer');
    }

    protected function getZipcodeFrom(){
        return $this->getConfig('shippingsettings/curacao_shipping/zipcodefrom');
    }

    protected function getStampsUrl(){
        return $this->getConfig('shippingsettings/curacao_shipping/stampsurl');
    }

    protected function getStampUser(){
        return $this->getConfig('shippingsettings/curacao_shipping/stampuser');
    }

    protected function getStampPass(){
        return $this->getConfig('shippingsettings/curacao_shipping/stamppass');
    }

    protected function getIntegrationId(){
        return $this->getConfig('shippingsettings/curacao_shipping/integrationid');
    }

    protected function getCost(){
        return $this->getConfig('shippingsettings/curacao_shipping/price');
    }
    /**
     * Function to return the rate using USPS
     */
    public function getUSPSRates($toZip, $weight = 10){
        
        $this->upsUrl = $this->getUSPSUrl();
        $this->uspsId = $this->getUSPSId();
        $this->uspsSize =  $this->getUSPSSize();
        $this->uspsContainer = $this->getUSPSContainer();
        $this->stampsUrl = $this->getStampsUrl();
        $this->stampUser = $this->getStampUser();
        $this->stampPass = $this->getStampPass();
        $this->integrationId = $this->getIntegrationId();
        $this->defaultCost = $this->getCost();

        $client = new \SoapClient($this->stampsUrl);

        $params = array(
            "Credentials" => array(
                                'Username'=>  $this->stampUser, 
                                'Password'=>  $this->stampPass,
                                'IntegrationID'=> $this->integrationId
                            ),
            "Rate" => array( 'ServiceType'=>'US-PM', 
                             'FromZIPCode'=> $this->zipcodefrom, 
                             'ToZIPCode'=> $toZip, 
                             'ToCountry'=>'US', 
                             'WeightLb'=> $weight, 
                             'ShipDate'=> date('Y-m-d'), 
                             'InsuredValue'=>0
                            )
            );
        
        $response = $client->__soapCall("GetRates", array($params));
        $rates = $response->Rates;

        foreach ($rates->Rate as $rate)
        {
            if($rate->ServiceType=='US-PM' & $rate->PackageType=='Package')
            {
                return $rate->Amount;
            }
        }
        return false;      
       
    }
    /**
    * Function to return the rate using UPS
    */
    public function getUPSRates($zipcode, $weight, $height = '', $length = '', $width = ''){
        if(!$weight){
            $weight = '50';
        }
        $_ups_package_types = array('02' => 'Package');

 	    $geoLocation = $this->geoCoordinateRepository->getById($zipcode);
        $toCity = $geoLocation->getCity();
        $toState = $geoLocation->getAbbr();
        $fromState = 'CA';

        $upsUrl = $this->getConfig('shippingsettings/curacao_ups/upsurl');
        $upsShipper = $this->getConfig('shippingsettings/curacao_ups/upsshipper');

        $this->startWriter('RatingServiceSelectionRequest', 'en-US');

        $this->_xmlWriter->startElement('Request');
        $this->_xmlWriter->writeElement('RequestAction', 'Shop');
        $this->_xmlWriter->writeElement('RequestOption', 'Shop');
        $this->_xmlWriter->endElement();

        $this->_xmlWriter->startElement('Shipment');
        
        $this->_xmlWriter->startElement('RateInformation');
        $this->_xmlWriter->writeElement('NegotiatedRatesIndicator');
        $this->_xmlWriter->endElement();

        $this->_xmlWriter->startElement('Shipper');
    	$this->_xmlWriter->writeElement('Name');
    	if ($upsShipper)
    		$this->_xmlWriter->writeElement('ShipperNumber',$upsShipper);
    	$this->_xmlWriter->startElement('Address');
    	$this->_xmlWriter->writeElement('AddressLine1');
    	$this->_xmlWriter->writeElement('city');
    	$this->_xmlWriter->writeElement('StateProvinceCode', $fromState);
    	$this->_xmlWriter->writeElement('PostalCode', $this->zipcodefrom);
    	$this->_xmlWriter->writeElement('CountryCode', 'US');
    	$this->_xmlWriter->writeElement('ResidentialAddressIndicator', true);
    	$this->_xmlWriter->endElement();
        $this->_xmlWriter->endElement(); //End of Shipper
        
        if (empty($toState)) $toState = 'CA';
    	$this->_xmlWriter->startElement('ShipTo');
    	$this->_xmlWriter->writeElement('Name');
   		$this->_xmlWriter->startElement('Address');
   		$this->_xmlWriter->writeElement('AddressLine1');
   		$this->_xmlWriter->writeElement('city');
   		$this->_xmlWriter->writeElement('StateProvinceCode', $toState);
   		$this->_xmlWriter->writeElement('PostalCode', $zipcode);
   		$this->_xmlWriter->writeElement('CountryCode', 'US');
   		$this->_xmlWriter->writeElement('ResidentialAddressIndicator', true);
   		$this->_xmlWriter->endElement();
   		$this->_xmlWriter->endElement();

        $this->writeDimensions($length,$width,$height,$weight,'02');

        $this->_xmlWriter->endElement();
 
        $xmlrequest = $this->endWriter();

        $xmlResponse = $this->getCurl($upsUrl . '/Rate', $this->setXMLAccessRequest() . $xmlrequest);
        $xml = simplexml_load_string($xmlResponse);
       
        // $xml = new \Magento\Framework\Simplexml\Config();
        // $xml->loadString($xmlResponse);
        // print_r($xmlResponse);

        $options = $xml->RatedShipment;
       
        $available_options = array();
        foreach ($options as $option){

            $code = $option->Service->Code;
           
            switch ($code){
    			case '03':	//UPS_GROUND
    				$rate = $option->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue ? (float) $option->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue : (float) $xml->RatedShipment->RatedPackage->TotalCharges->MonetaryValue;
    				$available_options[] = array(
    						'Code'			=> 'G',
    						'UPSCode'		=> '03',
    						'Service'		=> 'UPS_GROUND',
    						'Description'	=> 'UPS Ground',
    						'Rate'			=> $rate
    				);
                   
                    break;
                case '12':	//UPS_3_DAYS
                	$rate = $option->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue ? (float) $option->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue : (float) $xml->RatedShipment->RatedPackage->TotalCharges->MonetaryValue;
                	$available_options[] = array(
                			'Code'		=> 'G',
                    		'UPSCode'	=> '12',
                       		'Service'	=> 'UPS_3_DAYS',
                       		'Description'=> 'UPS 3 Days',
                       		'Rate'		=> $rate
                        );
                    break;
                case '02':	//UPS_2ND_DAY
                	$rate = $option->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue ? (float) $option->NegotiatedRates->NetSummaryCharges->GrandTotal->MonetaryValue : (float) $xml->RatedShipment->RatedPackage->TotalCharges->MonetaryValue;
                	$available_options[] = array(
                			'Code'		=> 'G',
                			'UPSCode'	=> '02',
                			'Service'	=> 'UPS_2ND_DAY',
                			'Description'=> 'UPS 2nd Day',
                			'Rate'		=> $rate
                       	);
                    break;

                default: break;
            }
        }
        return $available_options;
    }
    /**
     * Function for the starting of XML
     */
    private function startWriter($element, $lang = null) {
        if (is_string($element)) {

            $this->_xmlWriter = new \XMLWriter();
            $this->_xmlWriter->openMemory();
            $this->_xmlWriter->setIndent(true);
            $this->_xmlWriter->startDocument('1.0');
            $this->_xmlWriter->startElement($element);
            if (is_string($lang))
                $this->_xmlWriter->writeAttribute('xml:lang', $lang);
            return $this->_xmlWriter;
        } else
            throw new InvalidArgumentException('Invalid first element');
    }
    /**
     * Function for endWriter
     */
    private function endWriter(){
        $this->_xmlWriter->endElement();
        $this->_xmlWriter->endDocument();
        $xml = $this->_xmlWriter->outputMemory();
        $this->_xmlWriter = null;
        return $xml;
    }

    /**
    * Set xml access request
    *
    * @return void
    */
    protected function setXMLAccessRequest()
    {
        $userId = $this->getConfig('shippingsettings/curacao_ups/upsuserid');
        $userIdPass = $this->getConfig('shippingsettings/curacao_ups/upspassword');
        $accessKey = $this->getConfig('shippingsettings/curacao_ups/upsaccesskey');

        $this->startWriter('AccessRequest', 'en-US');
        $this->_xmlWriter->writeElement('AccessLicenseNumber', $accessKey);
        $this->_xmlWriter->writeElement('UserId', $userId);
        $this->_xmlWriter->writeElement('Password',$userIdPass);
        return $this->endWriter();
    }
    /**
     * Call the UPS url for getting result
     */
    private function getCurl($url, $data, $timeout = 300, $headers = array()) {
        $ch = curl_init($url);
        $default_headers = array(
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => $timeout,
            CURLOPT_POSTFIELDS => $data
        );
        curl_setopt_array($ch, $default_headers + $headers);
        $response = curl_exec($ch);
        curl_close($ch);

        return $response;
    }

    /**
    * Adds negotiated rate into the XMLWriter
    */
    private function writeNegotiatedRate() {
        $this->_xmlWriter->startElement('RateInformation');
        $this->_xmlWriter->writeElement('NegotiatedRatesIndicator');
        $this->_xmlWriter->endElement();
    }

    /**
     * Function to add the Dimensions
     */
    public function writeDimensions($length='10',$width='10',$height='10',$weight,$package_type = '02'){
        
        $this->_xmlWriter->startElement('Package');
       
        $this->_xmlWriter->startElement('PackagingType');
        $this->_xmlWriter->writeElement('Code', $package_type);
        $this->_xmlWriter->writeElement('Description','Package');
        $this->_xmlWriter->endElement();

        $this->_xmlWriter->startElement('PackageWeight');

        $this->_xmlWriter->startElement('UnitOfMeasurement');
        $this->_xmlWriter->writeElement('Code', 'LBS');
        $this->_xmlWriter->endElement();

        $this->_xmlWriter->writeElement('Weight', $weight);
        $this->_xmlWriter->endElement();

        // $this->_xmlWriter->startElement('Dimensions');
        // $this->_xmlWriter->writeElement('Length', $length);
        // $this->_xmlWriter->writeElement('Width', $width);
        // $this->_xmlWriter->writeElement('Height', $height);
        // $this->_xmlWriter->endElement();

        $this->_xmlWriter->endElement();
    }
}