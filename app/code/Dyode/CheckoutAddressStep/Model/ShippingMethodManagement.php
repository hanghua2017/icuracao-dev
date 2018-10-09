<?php
/**
 * Dyode_CheckoutAddressStep Magento2 Module.
 *
 * Adding new checkout step in the one page checkout.
 *
 * @package   Dyode
 * @module    Dyode_CheckoutAddressStep
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */
namespace Dyode\CheckoutAddressStep\Model;

use Dyode\CheckoutAddressStep\Api\Data\AddressInterface;
use Dyode\CheckoutAddressStep\Api\ShipmentEstimationInterface;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Catalog\Model\ProductRepository;
use Aheadworks\StoreLocator\Model\LocationFactory;

class ShippingMethodManagement implements ShipmentEstimationInterface
{
    // Constant Codes
    const DELIVERY_OPTION_SHIP_TO_HOME_ID = 1;
    const DELIVERY_OPTION_STORE_PICKUP_ID = 2;
    const STORE_LOCATION_CODE = 'store_location_code';


    /**
    * Zipcodes of all inventory locations of Curacao
    */
    private $_allLocationsZipcodes = array('01' => 90015, '09' => 91402, '16' => 90280, '22' => 90255, '29' => 92408, '33' => 90280, '35' => 92708, '38' => 91710, '51' => 92801, '40' => 85033, '57' => 85713, '64' => 89107);
    
    /**
    * \Magento\Framework\App\ResourceConnection
    */
    protected $_resourceConnection;

    /**
     * @var \Dyode\ArInvoice\Helper\Data
    */
    protected $_distHelper;

    /**
     * @var \Dyode\StoreLocator\Model\GeoCoordinateRepository
    */
    protected $_locationRepo;

    /**
     * @var \Dyode\Pilot\Model\Carrier\Pilot
    */
    protected $_pilot;

    /**
     * @var \Aheadworks\StoreLocator\Model\LocationFactory
     */
    protected $_locationFactory;
    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;
    /**
     * @var \Dyode\Checkout\Helper\Data $checkoutHelper
     */
    protected $_checkoutHelper;
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * ShippingMethodManagement constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param ProductRepository $productRepository
     * @param LocationFactory $locationFactory
     * @param \Dyode\ArInvoice\Helper\Data $distHelper
     * @param \Dyode\Pilot\Model\Carrier\Pilot $pilot
     * @param \Dyode\StoreLocator\Model\GeoCoordinateRepository $locationRepo
     * @param \Dyode\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     */
    public function __construct
    (
        CartRepositoryInterface $quoteRepository,
        \Magento\Checkout\Model\Session $checkoutSession,
        ProductRepository $productRepository,
        LocationFactory $locationFactory,  
        \Dyode\ArInvoice\Helper\Data $distHelper,
        \Dyode\Pilot\Model\Carrier\Pilot $pilot,
        \Dyode\StoreLocator\Model\GeoCoordinateRepository $locationRepo,
        \Dyode\Checkout\Helper\Data $checkoutHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection   
    )
    {
        $this->quoteRepository = $quoteRepository;
        $this->_checkoutSession = $checkoutSession;  
        $this->_productRepository = $productRepository;
        $this->_locationFactory = $locationFactory;
        $this->_distHelper = $distHelper;   
        $this->_locationRepo = $locationRepo;
        $this->_pilot = $pilot;
        $this->_checkoutHelper = $checkoutHelper;
        $this->_resourceConnection = $resourceConnection;
    }

    /**
     * @inheritdoc
     */
    public function estimateByExtendedAddress($cartId, AddressInterface $address)
    {
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);

        // no methods applicable for empty carts or carts with virtual products
        if ($quote->isVirtual() || 0 == $quote->getItemsCount()) {
            return [];
        }
        return $this->collectShippingInfo($quote, $address);
    }

    /**
     * @param \Magento\Quote\Model\Quote $quote
     * @param $address
     * @return array
     */
    private function collectShippingInfo(Quote $quote, $address)
    {
        $shippingInfo = [];
        # Get shipping zipcode
        $zipcode = $address->getZipCode();
        # Get all quote items 
        $quoteItems = $quote->getAllItems();
        # Check if quoteitems available
        if (!empty($quoteItems)) {
            foreach($quoteItems as $quoteItem) {
                # Get the delivery type and  quote item id and decide corresponding logic
                $quoteItemId = $quoteItem->getItemId();
                $deliveryType = $quoteItem->getDeliveryType();
                # store shipping/store location details to correponding quote item id
                if ($deliveryType == self::DELIVERY_OPTION_SHIP_TO_HOME_ID) {
                    $shippingInfo[$quoteItemId]['delivery_type'] = self::DELIVERY_OPTION_SHIP_TO_HOME_ID;
                    $shippingInfo[$quoteItemId]['data'] = $this->getShippingMethods($quoteItem, $zipcode);    
                }
                else if ($deliveryType == self::DELIVERY_OPTION_STORE_PICKUP_ID) {
                    $storeId = $quoteItem->getPickupLocation();
                    $shippingInfo[$quoteItemId]['delivery_type'] = self::DELIVERY_OPTION_STORE_PICKUP_ID;
                    $shippingInfo[$quoteItemId]['data'] = $this->getPickupStoreDetails($storeId);
                } 
                else {
                    echo "ERROR";
                }
            }
            # Return shipping method details in JSON format
            return $shippingInfo;
        }
    }

    /**
     * Get shipping methods according to the isfreight attribute and Distance 
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param [Integer] $zipcode
     * @return ARRAY
     */
    private function getShippingMethods($quoteItem, $zipcode) 
    {   
        // Default Shipment Config
        $rate = 10.54;
        $CarrierName = "UPS";
        $CarrierMethodCode = "Ground";
        // Get Latitude and Longitude of selected Address
        // $shipCoordinates = $this->_locationRepo->getById($zipcode);
        $shipCoordinates = $this->_locationRepo->getById("90045");
        $shipLat = $shipCoordinates->getLat();
        $shipLong = $shipCoordinates->getLng();
        # Get product Id from quote item 
        $productId = $quoteItem->getProductId();
        # Load product information using product id 
        $product = $this->getProductById($productId);
        
        # Check if product is Freight item if so use ADS momentum or Pilot
        if ($product->getIsfreight()) {
            /**
             * Find distance between destination and all store locations 
             * and see if its less than 80 km for any of them.
             */
            if ( $this->isDomestic($shipLat, $shipLong) ) {
                # ADS Momentum
                $CarrierName = "Freight";
                $CarrierMethodCode = "ADS Momentum";
                $productWeight = $product->getWeight();
                
                #Check if momentum and calculate rate
                if ($this->_checkoutHelper->checkMomentum($zipcode) && $productWeight) {
                    $rate = $this->_checkoutHelper->setQuoteItemPrice($productWeight);
                }
            }
            else {
                # Pilot
                $CarrierName = "Freight";
                $CarrierMethodCode = "Pilot";
                $rate = $this->_pilot->getPilotRatesSoap('90001',$zipcode);
            }

            
        }
        else {
            # Item is not freight so use USPS and UPS 
            $CarrierName = "USPS";
            $CarrierMethodCode = "Priority";
            $rate = 11;
        }

        return ['CarrierName'=>$CarrierName,'CarrierMethodCode'=>$CarrierMethodCode,'rate'=>$rate];
    }

    /**
     * Get store locations according to store id
     *
     * @param [Integer] $store_location_id
     * @return Array
     */
    private function getPickupStoreDetails($store_location_id) {
        $location = $this->_locationFactory->create();
        # Get corresponding location Data
        $locationData = $location->load($store_location_id, self::STORE_LOCATION_CODE)->getData();
        # Return location details as json
        return $locationData;
    }


    /**
     * Check if distance to customer address is less than 80 for any store location
     *
     * @param [Float] $shippingZipCodeLat
     * @param [Float] $shippingZipCodeLng
     * @return boolean
     */
    private function isDomestic($shippingZipCodeLat, $shippingZipCodeLng) 
    {
        foreach ($this->_allLocationsZipcodes as $locationCode => $zipCode)
        {
            #Logger
            $om =   \Magento\Framework\App\ObjectManager::getInstance();
            $logger = $om->get("Psr\Log\LoggerInterface");
            $logger->info("Distance function");
            
            $query = "SELECT * FROM `locations` WHERE `zip` = $zipCode ";
            $resourceConnection = $this->_resourceConnection->getConnection();
            $result = $resourceConnection->fetchAll($query);
            if ($result) {
                $storeZipCodeLat = $result[0]['lat'];
                $storeZipCodeLng = $result[0]['lng'];
                $distance = $this->_distHelper->getDistance($shippingZipCodeLat, $shippingZipCodeLng, $storeZipCodeLat, $storeZipCodeLng);
                $logger->info("Distance:".$distance);
                if (round($distance) <= 80) {
                    return true;
                }
            }
        }
    }


    /**
     * Get product details from product id.
     *
     * @param [Integer] $id
     * @return Magento\Catalog\Model\ProductRepository
     */
    private function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }
}