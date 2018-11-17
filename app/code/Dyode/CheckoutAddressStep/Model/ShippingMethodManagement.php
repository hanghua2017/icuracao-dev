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
use Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate\Collection;
use Magento\Framework\DataObject;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item as QuoteItem;
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
    

    protected $usps_std_rate;

    protected $ups_std_rate;

    protected $set_usps;

    /**
     * Zipcodes of all inventory locations of Curacao
     */
    private $_allLocationsZipCodes = [
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
     * @var \Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate\Collection
     */
    protected $geoCoordinateCollection;

    /**
     * Holds geo-locations corresponds to the available pickup stores.
     *
     * @var \Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate\Collection
     */
    protected $storeLocations;

    /**
     * ShippingMethodManagement constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Aheadworks\StoreLocator\Model\LocationFactory $locationFactory
     * @param \Dyode\CheckoutAddressStep\Helper\Data $shipHelper
     * @param \Dyode\ArInvoice\Helper\Data $distHelper
     * @param \Dyode\Pilot\Model\Carrier\Pilot $pilot
     * @param \Dyode\AdsMomentum\Model\Carrier\AdsMomentum $momentum
     * @param \Dyode\StoreLocator\Model\GeoCoordinateRepository $locationRepo
     * @param \Dyode\Checkout\Helper\Data $checkoutHelper
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate\Collection $geoCoordinateCollection
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
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        Collection $geoCoordinateCollection
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
        $this->geoCoordinateCollection = $geoCoordinateCollection;
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
        $zipCode = $address->getZipCode();
        $quoteItems = $quote->getAllItems();

        if (!empty($quoteItems)) {
            foreach ($quoteItems as $quoteItem) {

                if ($quoteItem->getProductType() === 'virtual') {
                    continue;
                }

                $quoteItemId = $quoteItem->getItemId();
                $deliveryType = $quoteItem->getDeliveryType();

                if ($deliveryType == self::DELIVERY_OPTION_SHIP_TO_HOME_ID) {
                    $shippingInfo[$quoteItemId] = $this->getShippingMethods($quoteItem, $zipCode);
                }

                if ($deliveryType == self::DELIVERY_OPTION_STORE_PICKUP_ID) {
                    $storeId = $quoteItem->getPickupLocation();
                    $shippingInfo[$quoteItemId] = $this->getPickupStoreDetails($storeId, $quoteItemId);
                }

            }
        }

        return $shippingInfo;
    }

    /**
     * Get shipping methods according to the isfreight attribute and Distance
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param $zipCode
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getShippingMethods($quoteItem, $zipCode)
    {
        $shipCoordinates = $this->_locationRepo->getById($zipCode);
        $product = $this->getProductById($quoteItem->getProductId());

        //Condition for Freight Items
        if ($product->getFreight()) {
            if ($this->isDomestic($shipCoordinates->getLat(), $shipCoordinates->getLng())) {
                return $this->adsMomentumShippingDetails($quoteItem, $product, $zipCode);
            }
            // Return the SEKO rate
            return $this->sekoShippingDetails($quoteItem,$product,$zipCode);
        }
       
        //Condition for Shiprate Items is Domestic
        if ($product->getShprate() == 'Domestic') {
            if ($this->isDomestic($shipCoordinates->getLat(), $shipCoordinates->getLng())) {
                return $this->adsMomentumShippingDetails($quoteItem, $product, $zipCode);
            }
            // Return the SEKO rate
            return $this->sekoShippingDetails($quoteItem,$product,$zipCode);
        }

        //if product weight is less than 70lbs add usps also
        if ( $product->getWeight() <= 70 ) {
            //Get UPS price details
            $uspsDetails = $this->uspsShippingDetails($quoteItem, $product, $zipCode);
        }
       
        return $this->upsShippingDetails($quoteItem, $product, $zipCode);
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
        $locationData = $location->load($store_location_id, self::STORE_LOCATION_CODE)->getData();

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
        foreach ($this->_allLocationsZipCodes as $locationCode => $zipCode) {
            $storeLocation = $this->storeLocationsList()->getItemById($zipCode);

            if ($storeLocation) {
                $distance = $this->_distHelper->getDistance(
                    $shippingZipCodeLat, $shippingZipCodeLng, $storeLocation->getLat(), $storeLocation->getLng()
                );

                if (round($distance) <= 80) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @return \Dyode\StoreLocator\Model\ResourceModel\GeoCoordinate\Collection
     */
    protected function storeLocationsList()
    {
        if (!$this->storeLocations) {
            $this->storeLocations = $this->geoCoordinateCollection
                ->addFieldToFilter('zip', ['in' => $this->_allLocationsZipCodes])
                ->setPageSize(count($this->_allLocationsZipCodes))
                ->setCurPage(1);
        }

        return $this->storeLocations;
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

    /**
     * Prepare ADS Momentum shipping details.
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param $product
     * @param string|int $zipCode
     * @return array
     */
    protected function adsMomentumShippingDetails(QuoteItem $quoteItem, $product, $zipCode)
    {
        $shippingConfig = $this->getCarriersConfig();
        $productWeight = $product->getWeight();
        $momentumCode = $this->_momentum->getCode();
        $adsCarrierDetails = $shippingConfig[$momentumCode];
        $carrierTitle = $adsCarrierDetails['title'];
        $carrierName = $adsCarrierDetails['name'];
        $rate = $adsCarrierDetails['price'];


        $shippingData = new DataObject([
            'quote_item_id'  => $quoteItem->getItemId(),
            'carrier_code'   => $momentumCode,
            'method_code'    => $momentumCode,
            'carrier_title'  => $carrierTitle,
            'method_title'   => $carrierName,
            'amount'         => $rate,
            'base_amount'    => $rate,
            'available'      => true,
            'error_message'  => '',
            'price_excl_tax' => '',
            'price_incl_tax' => '',

        ]);

        return $this->prepareShippingInfo($shippingData);
    }

     /**
     * Prepare SEKO shipping method information.
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param $product
     * @param string|int $zipCode
     * @return array
     */
    protected function sekoShippingDetails(QuoteItem $quoteItem, $product, $zipCode)
    {
        $carrierCode = 'sekoshipping';
        $productWeight = $product->getWeight();       

        $shippingConfig = $this->getCarriersConfig();
        $sekoDetails  = $shippingConfig[$carrierCode];
        $carrierTitle = $sekoDetails['title'];
        $carrierName = $sekoDetails['name'];

        $rate = $sekoDetails['price'];


        $shippingData = new DataObject([
            'quote_item_id'  => $quoteItem->getItemId(),
            'carrier_code'   => $carrierCode,
            'method_code'    => $carrierCode,
            'carrier_title'  => $carrierTitle,
            'method_title'   => $carrierName,
            'amount'         => $rate,
            'base_amount'    => $rate,
            'available'      => true,
            'error_message'  => '',
            'price_excl_tax' => '',
            'price_incl_tax' => '',

        ]);

        return $this->prepareShippingInfo($shippingData);
    }

    /**
     * Prepare Pilot shipping method details.
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param \Magento\Catalog\Model\Product $product
     * @param string|int $zipCode
     * @return array
     */
    protected function pilotShippingDetails(QuoteItem $quoteItem, $product, $zipCode)
    {
        $shippingConfig = $this->getCarriersConfig();
        $pilotCode = $this->_pilot->getCode();
        $pilotDetails = $shippingConfig[$pilotCode];
        $carrierTitle = $pilotDetails['title'];
        $carrierName = $pilotDetails['name'];
        if($product->getWeight() != null)
            $productWeight = $product->getWeight();

        $rate = $this->_pilot->getPilotRatesSoap('90001', $zipCode, $productWeight);


        $shippingData = new DataObject([
            'quote_item_id'  => $quoteItem->getItemId(),
            'carrier_code'   => $pilotCode,
            'method_code'    => $pilotCode,
            'carrier_title'  => $carrierTitle,
            'method_title'   => $carrierName,
            'amount'         => $rate,
            'base_amount'    => $rate,
            'available'      => true,
            'error_message'  => '',
            'price_excl_tax' => '',
            'price_incl_tax' => '',

        ]);

        return $this->prepareShippingInfo($shippingData);
    }

    /**
     * Prepare USPS shipping method information.
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param $product
     * @param string|int $zipCode
     * @return array
     */
    protected function uspsShippingDetails(QuoteItem $quoteItem, $product, $zipCode)
    {
        $carrierCode = 'usps';
        $carrierTitle = __('USPS');
        $carrierName = __('Standard');
        $productWeight = $product->getWeight();
        if ( $productWeight <= 0) {
            $productWeight = 10;
        }

        $rate = $this->shipHelper->getUSPSRates($zipCode, $productWeight);

        $this->usps_std_rate = $rate;

        $shippingData = new DataObject([
            'quote_item_id'  => $quoteItem->getItemId(),
            'carrier_code'   => $carrierCode,
            'method_code'    => $carrierCode,
            'carrier_title'  => $carrierTitle,
            'method_title'   => $carrierName,
            'amount'         => $rate,
            'base_amount'    => $rate,
            'available'      => true,
            'error_message'  => '',
            'price_excl_tax' => '',
            'price_incl_tax' => '',

        ]);

        return $this->prepareShippingInfo($shippingData);
    }

    /**
     * Prepare UPS shipping method information.
     *
     * @param \Magento\Quote\Model\Quote\Item $quoteItem
     * @param $product
     * @param string|int $zipCode
     * @return array
     */
    protected function upsShippingDetails(QuoteItem $quoteItem, $product, $zipCode)
    {
        $productWeight = $product->getWeight();
        if ( $productWeight <= 0) {
            $productWeight = 1;
        }
        if ( $productWeight >= 150 ) {
            $productWeight = 147;
        }
        if ( $productWeight >= 150 ){
           $productWeight = 147;
        }
        $shippingMethods = $this->shipHelper->getUPSRates($zipCode, $productWeight);
        $deliveryMethods = $this->prepareUpsShippingData($shippingMethods, $quoteItem->getItemId());

        return [
            'quote_item_id'    => $quoteItem->getItemId(),
            'delivery_option'  => self::DELIVERY_OPTION_SHIP_TO_HOME,
            'delivery_methods' => $deliveryMethods,
        ];
    }

    /**
     * Prepare Shipping method data for UPS to checkout
     *
     * @param $shippingMethods
     * @param $quoteItemId
     * @return array
     */
    protected function prepareUpsShippingData($shippingMethods, $quoteItemId)
    {
        $deliveryMethods = [];

        foreach ($shippingMethods as $method) {
            switch ($method['UPSCode']) {
                case '03':
                    //Check UPS price is less than USPS
                    $this->ups_std_rate = $method['Rate'];
                    $result = 0;

                    if(isset($this->usps_std_rate)) {
                        $result = $this->findStandardDeliveryRate();
                    }

                    if($result == 0) {
                        $deliveryMethods[] = [
                            'quote_item_id' => $quoteItemId,
                            "carrier_code"  => 'ups',
                            "method_code"   => 'GND',
                            "carrier_title" => 'UPS',
                            "method_title"  => 'Standard Delivery',
                            "amount"        => $method['Rate'],
                            "base_amount"   => $method['Rate'],
                            "available"     => true,
                            "error_message" => '',
                        ];
                    } else {
                        $deliveryMethods[] = [
                            'quote_item_id' => $quoteItemId,
                            "carrier_code"  => 'usps',
                            "method_code"   => 'usps',
                            "carrier_title" => 'Standard Delivery',
                            "method_title"  => 'Standard Delivery',
                            "amount"        => $this->usps_std_rate,
                            "base_amount"   => $this->usps_std_rate,
                            "available"     => true,
                            "error_message" => '',
                        ];
                    }
                    break;
                // case '12':
                //     $deliveryMethods[] = [
                //         'quote_item_id' => $quoteItemId,
                //         "carrier_code"  => 'ups',
                //         "method_code"   => '2DA',
                //         "carrier_title" => 'UPS',
                //         "method_title"  => '2nd Day',
                //         "amount"        => $method['Rate'],
                //         "base_amount"   => $method['Rate'],
                //         "available"     => true,
                //         "error_message" => '',
                //     ];
                //     break;
                case '02':
                    $deliveryMethods[] = [
                        'quote_item_id' => $quoteItemId,
                        "carrier_code"  => 'ups',
                        "method_code"   => '3DS',
                        "carrier_title" => 'UPS',
                        "method_title"  => 'Expedited',
                        "amount"        => $method['Rate'],
                        "base_amount"   => $method['Rate'],
                        "available"     => true,
                        "error_message" => '',
                    ];
                    break;
            }

        }
        return $deliveryMethods;
    }

    /**
     * Prepare the shipping array response from the shipping data object.
     *
     * @param \Magento\Framework\DataObject $shippingMethodInfo
     * @return array
     */
    protected function prepareShippingInfo(DataObject $shippingMethodInfo)
    {
        return [
            'quote_item_id'    => $shippingMethodInfo->getQuoteItemId(),
            'delivery_option'  => self::DELIVERY_OPTION_SHIP_TO_HOME,
            'delivery_methods' => [
                [
                    'quote_item_id'  => $shippingMethodInfo->getQuoteItemId(),
                    "carrier_code"   => $shippingMethodInfo->getCarrierCode(),
                    "method_code"    => $shippingMethodInfo->getMethodCode(),
                    "carrier_title"  => $shippingMethodInfo->getCarrierTitle(),
                    "method_title"   => $shippingMethodInfo->getMethodTitle(),
                    "amount"         => $shippingMethodInfo->getAmount(),
                    "base_amount"    => $shippingMethodInfo->getBaseAmount(),
                    "available"      => $shippingMethodInfo->getAvailable(),
                    "error_message"  => $shippingMethodInfo->getErrorMessage(),
                    "price_excl_tax" => $shippingMethodInfo->getPriceExclTax(),
                    "price_incl_tax" => $shippingMethodInfo->getPriceInclTax(),
                ],
            ],
        ];
    }
    /**
     * Find the lowest price for standard delivery for UPS and USPS
     * @param USPSRates
     * @param UPSRates
     * @return array
     */
    public function findStandardDeliveryRate()
    {
        $this->set_usps = 0;
      
        if($this->usps_std_rate < $this->ups_std_rate){
            $this->set_usps = 1;
        }
        
        return $this->set_usps;
    }
}
