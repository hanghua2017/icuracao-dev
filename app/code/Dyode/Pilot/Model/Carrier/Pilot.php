<?php


namespace Dyode\Pilot\Model\Carrier;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

class Pilot extends \Magento\Shipping\Model\Carrier\AbstractCarrier implements
    \Magento\Shipping\Model\Carrier\CarrierInterface
{

    protected $_code = 'pilot';

    protected $_isFixed = true;

    protected $_rateResultFactory;

    protected $_rateMethodFactory;
    /**
     * Shipment Item Details
     */
    private $length;
    private $width;
    private $height;
    private $weight;
    private $zipto;
    private $zipfrom;
    private $writer;
    

    /**
     * Constructor
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        $shippingPrice = $this->getConfigData('price');

        $result = $this->_rateResultFactory->create();

        if ($shippingPrice !== false) {
            $method = $this->_rateMethodFactory->create();

            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));

            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));

            if ($request->getFreeShipping() === true || $request->getPackageQty() == $this->getFreeBoxes()) {
                $shippingPrice = '0.00';
            }

            $method->setPrice($shippingPrice);
            $method->setCost($shippingPrice);

            $result->append($method);
        }

        return $result;
    }

    /**
     * getAllowedMethods
     *
     * @param array
     */
    public function getAllowedMethods()
    {
        return ['flatrate' => $this->getConfigData('name')];
    }

    /**
     * 
     * Calculates Pilot Shipping Quotes
     * 
     */

    
    public function getPilotRatesSoap($fromZip = '90015', $toZip = '12345', $weight = 1, $width = 10, $height = 10, $length = 10){
        
        $client = new SoapClient("http://ws.pilotdelivers.com/tms2.1/tms/PilotServiceRequest.asmx?wsdl");
        
                
        $xml = '<ns2:RateAllServices xmlns:ns2="http://www.pilotair.com/">
         <ns2:cQuote>
            <ns1:dsTQSQuote xmlns:ns1="http://tempuri.org/dsTQSQuote.xsd">
               <ns1:TQSQuote>
                            <LocationID>11156674</LocationID>
                            <TransportByAir>false</TransportByAir>
                            <TariffHeaderID>22498</TariffHeaderID>
                            <ns1:Shipper>
                               <ns1:State/>
                               <ns1:Zipcode>'.$fromZip.'</ns1:Zipcode>
                               <Country />
                              </ns1:Shipper>
                     <ns1:Consignee>
                        <ns1:State/>
                        <ns1:Zipcode>'.$toZip.'</ns1:Zipcode>
                        <Country />
                     </ns1:Consignee>
                  <ns1:LineItems>
                     <ns1:LineRow>0</ns1:LineRow>
                     <ns1:Pieces>1</ns1:Pieces>
                     <ns1:Weight>'.$weight.'</ns1:Weight>
                     <ns1:Length>'.$length.'</ns1:Length>
                     <ns1:Width>'.$width.'</ns1:Width>
                     <ns1:Height>'.$height.'</ns1:Height>
                  </ns1:LineItems>
               </ns1:TQSQuote>
            </ns1:dsTQSQuote>
         </ns2:cQuote>
        </ns2:RateAllServices>';
        
        $params = new SoapVar($xml, XSD_ANYXML);
        
        $response = $client->__soapCall("RateAllServices", array($params));
        
        $xmlString = $response->RateAllServicesResult->dsQuote->any;
        

        // Pilot Sucks , returned bouble xml, let's split them
        $xmlString =  substr($xmlString, strpos($xmlString, '<diffgr:diffgram'));
       
       
        $xml = simplexml_load_string($xmlString);
        $json = json_encode($xml);
        $array = json_decode($json,TRUE);
        
        $shippingServices = $array['dsTQSQuote']['TQSQuote']['Quote'];
        foreach($shippingServices as $service){
            if($service['Service'] == 'BA')
            {
                $pilotBest = $service;
            }
        }
        
        return $pilotBest['TotalQuote'];
    }
}
