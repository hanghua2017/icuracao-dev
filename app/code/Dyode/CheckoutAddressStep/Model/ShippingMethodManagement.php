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
use Dyode\CheckoutAddressStep\Helper\Data;

class ShippingMethodManagement implements ShipmentEstimationInterface
{
    // Constant Codes
    const DELIVERY_OPTION_SHIP_TO_HOME_ID = 1;
    const DELIVERY_OPTION_STORE_PICKUP_ID = 2;
    const DELIVERY_OPTION_SHIP_TO_HOME = "ship_to_home";
    const DELIVERY_OPTION_STORE_PICKUP = "store_pickup";
    const DEFAULT_SHIPPING_RATE = 10.24;
    const STORE_LOCATION_CODE = 'store_location_code';
    const USPS_WITH = 3;
    const USPS_PRICE_LIMIT = 200;

    /**
     * Zipcodes of all inventory locations of Curacao
     */
    private $_allLocationsZipcodes = [
        '01' => 90015,
        '09' => 91402,
        '16' => 90280,
        '22' => 90255,
        '29' => 92408,
        '33' => 90280,
        '35' => 92708,
        '38' => 91710,
        '51' => 92801,
        '40' => 85033,
        '57' => 85713,
        '64' => 89107,
    ];

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
     * @var \Dyode\AdsMomentum\Model\Carrier\AdsMomentum
     */
    protected $_momentum;

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
     * @var \Magento\Shipping\Model\Config $shippingConfig
     */
    protected $_shippingConfig;
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var type \Dyode\CheckoutAddressStep\Helper\Data $shipHelper
     */
    protected $shipHelper;

    /**
     * ShippingMethodManagement constructor.
     *
     * @param CartRepositoryInterface $quoteRepository
     * @param ProductRepository $productRepository
     * @param LocationFactory $locationFactory
     * @param \Dyode\ArInvoice\Helper\Data $distHelper
     * @param \Dyode\Pilot\Model\Carrier\Pilot $pilot
     * @param \Dyode\AdsMomentum\Model\Carrier\AdsMomentum $momentum
     * @param \Dyode\StoreLocator\Model\GeoCoordinateRepository $locationRepo
     * @param \Dyode\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Dyode\CheckoutAddressStep\Helper\Data $shipHelper
     */
    public function __construct
    (
        CartRepositoryInterface $quoteRepository,
        ProductRepository $productRepository,
        LocationFactory $locationFactory,
        Data $shipHelper,
        \Dyode\ArInvoice\Helper\Data $distHelper,
        \Dyode\Pilot\Model\Carrier\Pilot $pilot,
        \Dyode\AdsMomentum\Model\Carrier\AdsMomentum $momentum,
        \Dyode\StoreLocator\Model\GeoCoordinateRepository $locationRepo,
        \Dyode\Checkout\Helper\Data $checkoutHelper,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->_productRepository = $productRepository;
        $this->_locationFactory = $locationFactory;
        $this->_distHelper = $distHelper;
        $this->_locationRepo = $locationRepo;
        $this->_pilot = $pilot;
        $this->_momentum = $momentum;
        $this->_checkoutHelper = $checkoutHelper;
        $this->_resourceConnection = $resourceConnection;
        $this->_shippingConfig = $shippingConfig;
        $this->_scopeConfig = $scopeConfig;
        $this->shipHelper = $shipHelper;
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
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function collectShippingInfo(Quote $quote, $address)
    {
        $shippingInfo = [];
        // Get shipping zipcode
        $zipcode = $address->getZipCode();
        // Get all quote items 
        $quoteItems = $quote->getAllItems();
        // Check if quoteitems available
        if (!empty($quoteItems)) {
            foreach ($quoteItems as $quoteItem) {

                # Get the delivery type and  quote item id and decide corresponding logic
                $quoteItemId = $quoteItem->getItemId();
                $deliveryType = $quoteItem->getDeliveryType();

                # store shipping/store location details to correponding quote item id
                if ($deliveryType == self::DELIVERY_OPTION_SHIP_TO_HOME_ID) {
                    $shippingInfo[$quoteItemId] = $this->getShippingMethods($quoteItem, $zipcode);
                }

                if ($deliveryType == self::DELIVERY_OPTION_STORE_PICKUP_ID) {
                        $storeId = $quoteItem->getPickupLocation();
                        $shippingInfo[$quoteItemId] = $this->getPickupStoreDetails($storeId, $quoteItemId);
                }

            }
            # Return shipping method details in JSON format
            return $shippingInfo;
        }
    }

    /**
     * Get shipping methods according to the isfreight attribute and Distance
     *
     * @param $quoteItem
     * @param $zipcode
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getShippingMethods($quoteItem, $zipcode)
    {
        // Default Shipment Config
        $rate = self::DEFAULT_SHIPPING_RATE;
        // Get shipping carrier details
        $shippingConfig = $this->getCarriersConfig();
        // Get Latitude and Longitude of selected Address
        $shipCoordinates = $this->_locationRepo->getById($zipcode);
        $shipLat = $shipCoordinates->getLat();
        $shipLong = $shipCoordinates->getLng();
       
        // Get quote item id
        $quoteItemId = $quoteItem->getItemId();
        // Get product Id from quote item 
        $productId = $quoteItem->getProductId();
        // Load product information using product id 
        $product = $this->getProductById($productId);
        $productWeight = $product->getWeight();
        $productPrice = $product->getPrice();
        // Check if product is Freight item if so use ADS momentum or Pilot
        if ($product->getIsfreight()) {

            /**
             * Find distance between destination and all store locations
             * and see if its less than 80 km for any of them.
             */
            if ($this->isDomestic($shipLat, $shipLong)) {
                
                // ADS Momentum
                $adsCarrierDetails = $shippingConfig[$this->_momentum->getCode()];
                $carrierCode = $this->_momentum->getCode();
                $carrierMethodCode = $this->_momentum->getCode();
                $carrierTitle = $adsCarrierDetails['title'];
                $carrierName = $adsCarrierDetails['name'];

                // Check if momentum and calculate rate
                if ($this->_checkoutHelper->checkMomentum($zipcode) && $productWeight) {
                    $rate = $this->_checkoutHelper->setQuoteItemPrice($productWeight);
                }
            } else {
                // Pilot
                $pilotDetails = $shippingConfig[$this->_pilot->getCode()];
                // Prepare shipment data
                $carrierCode = $this->_momentum->getCode();
                $carrierMethodCode = $this->_momentum->getCode();
                $carrierTitle = $pilotDetails['title'];
                $carrierName = $pilotDetails['name'];
                $rate = $this->_pilot->getPilotRatesSoap('90001', $zipcode);
            }
        } else {
            // Item is not freight so use USPS and UPS 
            $upsWith = 3;
            $toCity = $shipCoordinates->getCity();
            $toState = $shipCoordinates->getAbbr();
            if(in_array($toState, ['CA','NV','AZ'])){
                $upsWith = 11;
            }
            //Check for USPS
            switch(true){
                case (( $productWeight < $upsWith ) && ($productPrice < self::USPS_PRICE_LIMIT)):
                    $carrierCode = 'usps';
                    $carrierMethodCode = 'usps';
                    $carrierTitle = 'USPS';
                    $carrierName = 'Priority';
                    $rate = $this->shipHelper->getUSPSRates($zipcode,$productWeight);
                    break;
                case (( $productWeight < $upsWith ) && ($productPrice > self::USPS_PRICE_LIMIT)):
                    $carrierCode = 'ups';
                    $carrierMethodCode = 'ups';
                    $carrierTitle = 'UPS';
                    $carrierName = 'Ground';
                    $rate = self::DEFAULT_SHIPPING_RATE;
                    break;
                default: 
                    $carrierCode = 'ups';
                    $carrierMethodCode = 'ups';
                    $carrierTitle = 'UPS';
                    $carrierName = 'Ground';
                    $rate = self::DEFAULT_SHIPPING_RATE;
                    break;
                    
                }
                      
        }

        return [
            'quote_item_id'    => $quoteItemId,
            'delivery_option'  => self::DELIVERY_OPTION_SHIP_TO_HOME,
            'delivery_methods' => [
                [
                    'quote_item_id'  => $quoteItemId,
                    "carrier_code"   => $carrierCode,
                    "method_code"    => $carrierMethodCode,
                    "carrier_title"  => $carrierTitle,
                    "method_title"   => $carrierName,
                    "amount"         => $rate,
                    "base_amount"    => $rate,
                    "available"      => true,
                    "error_message"  => "",
                    "price_excl_tax" => 5,
                    "price_incl_tax" => 5,
                ],
            ],
        ];
    }

    /**
     * Get store locations according to store id
     *
     * @param integer $store_location_id
     * @return array
     */
    private function getPickupStoreDetails($store_location_id, $quoteItemId)
    {
        $location = $this->_locationFactory->create();
        // Get corresponding location Data
        $locationData = $location->load($store_location_id, self::STORE_LOCATION_CODE)->getData();
        // Return location details as json

        return [
            'delivery_option'  => 'store_pickup',
            'quote_item_id'    => $quoteItemId,
            'delivery_methods' => [],
            'store_info'       => [
                'id'      => $locationData['location_id'],
                'code'    => $locationData['store_location_code'],
                'name'    => $locationData['title'],
                'address' => [
                    'title'  => $locationData['title'],
                    'street' => $locationData['street'],
                    'city'   => $locationData['city'],
                    'zip'    => $locationData['zip'],
                    'phone'  => $locationData['phone'],
                ],
            ],
        ];
    }

    /**
     * Check if distance to customer address is less than 80 for any store location
     *
     * @param float $shippingZipCodeLat
     * @param float $shippingZipCodeLng
     * @return boolean
     */
    private function isDomestic($shippingZipCodeLat, $shippingZipCodeLng)
    {
        foreach ($this->_allLocationsZipcodes as $locationCode => $zipCode) {
            $query = "SELECT * FROM `locations` WHERE `zip` = $zipCode ";
            $resourceConnection = $this->_resourceConnection->getConnection();
            $result = $resourceConnection->fetchAll($query);
            if ($result) {
                $storeZipCodeLat = $result[0]['lat'];
                $storeZipCodeLng = $result[0]['lng'];
                $distance = $this->_distHelper->getDistance(
                    $shippingZipCodeLat, $shippingZipCodeLng, $storeZipCodeLat, $storeZipCodeLng
                );

                if (round($distance) <= 80) {
                    return true;
                }
            }
        }
    }

    /**
     * @param string $store
     * @return array
     */
    private function getCarriersConfig($store = null)
    {
        return $this->_scopeConfig->getValue(
            'carriers', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store
        );
    }

    /**
     * Get product details from product id.
     *
     * @param $id
     * @return \Magento\Catalog\Api\Data\ProductInterface|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }
}
