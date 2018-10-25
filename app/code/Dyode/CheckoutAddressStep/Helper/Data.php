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
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * 
     */   
    public function __construct(
        Context $context       
    ){
        parent::__construct($context);
        $this->upsUrl = $this->getUSPSUrl();
        $this->uspsId = $this->getUSPSId();
        $this->uspsSize =  $this->getUSPSSize();
        $this->uspsContainer = $this->getUSPSContainer();
        $this->zipcodefrom = $this->getZipcodeFrom();
        $this->stampsUrl = $this->getStampsUrl();
        $this->stampUser = $this->getStampUser();
        $this->stampPass = $this->getStampPass();
        $this->integrationId = $this->getIntegrationId();
        $this->defaultCost = $this->getCost();
    }

    public function getConfig($config_path)
    {
        return $this->scopeConfig->getValue(
            $config_path,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

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
    public function getUSPSRates($toZip, $weight = 1){
        
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

}