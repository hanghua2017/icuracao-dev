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
     * Zipcodes of all inventory locations of Curacao
     */
    private $_allLocationsZipcodes = array('01' => 90015, '09' => 91402, '16' => 90280, '22' => 90255, '29' => 92408, '33' => 90280, '35' => 92708, '38' => 91710, '51' => 92801, '40' => 85033, '57' => 85713, '64' => 89107);

    /**
     * Domestic Location
     */
    private $_domesticLocation = '06';

    /**
     * Order Items
     */
    private $_orderItems;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     * @var \Magento\Sales\Model\OrderRepository $orderRepository
     **/
    protected $_orderRepository;

    /**
     * Constructor
     *
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Catalog\Model\ProductRepository $productRepository
    ) {
        $this->_orderRepository = $orderRepository;
        $this->jsonHelper = $jsonHelper;
        $this->_productRepository = $productRepository;
    }

    /**
     * Initialize Rest Api Connection
     */
    public function initRestApiConnect($url)
    {
        /**
         * Init Curl
         */
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /**
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
        /**
         * Initialize Rest Api Connection
         */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/CreateRevEstimate";
        $ch = $this->initRestApiConnect($url);
        /**
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
        /**
         * Initialize Rest Api Connection
         */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/webDownpayment";
        $ch = $this->initRestApiConnect($url);
        /**
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
        /**
         * Initialize Rest Api Connection
         */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/SupplyInvoice";
        $ch = $this->initRestApiConnect($url);
        /**
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
        } elseif (strlen($accountNumber) < 7) {
            $accountNumber = str_pad($accountNumber, 7, "0", STR_PAD_LEFT);
            return $accountNumberFormatted = substr_replace($accountNumber, "-", 3, 0);
        } else {
            return $accountNumberFormatted = $accountNumber;
        }
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
            if ($shippingStreet == $defaultStreet && $shippingZip == $defaultZip){
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
     * Inventory Level using API -> InventoryLevel
     * 
     * @return array 
     */
    public function inventoryLevel($itemId, $locations)
    {
        /**
         * Initialize Rest Api Connection
         */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/InventoryLevel?item_id=$itemId&locations=$locations";
        $ch = $this->initRestApiConnect($url);
        /**
         * Set Post Data
         */
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        /**
         * Get Response Data
         */
        $response = curl_exec($ch);
        curl_close($ch);
        return json_decode($response);
    }

    /**
     * getSetItems() returns a encoded json of items quantity in each location
     */
    public function getSetItems($itemId)
    {
        $ch = curl_init("https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/getSetItems?item_id=$itemId");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "X-Api-Key: TEST-WNNxLUjBxA78J7s"));

        $result = curl_exec($ch);

        return $result;
    }

    /**
     * Load product by Product Id
     */
    private function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    /**
     * Load product by SKU
     */
    private function getProductBySku($sku)
    {
        return $this->_productRepository->get($sku);
    }

    /**
     * getGroupedLocation() returns a grouped location for items
     */
    public function getGroupedLocation($item) {
        $skuInv = array();
        foreach($this->_orderItems as $item) {
        $threshold = 0;
        $itemSku = $item->getSku();
        #Load Product Info
        $product = $this->getProductBySku($itemSku);
        $qty = (int) $item->getQtyOrdered();

        if ($product->getVendorid() == '2139' && strtolower($product->getShprate()) != 'domestic' && $product->getSet() != 1) {
        $skuInv[$itemSku] = $this->getSkuInventory($itemSku);
        $pending = $this->getPendingEstimates($sku);
        # Iterate through pending orders
        foreach ($pending as $valueRunningEstimates) {
        if (isset($skuInv[$sku][$valueRunningEstimates['store_location']])) {
        if ($skuInv[$sku][$valueRunningEstimates['store_location']] == $actualLocation) {
        $skuInv[$sku][$valueRunningEstimates['store_location']] -= ($valueRunningEstimates['pending'] - $qty);
        } else {
        $skuInv[$sku][$valueRunningEstimates['store_location']] -= $valueRunningEstimates['pending'];
        }
        if ($skuInv[$sku][$valueRunningEstimates['store_location']] < 0) {
        $skuInv[$sku][$valueRunningEstimates['store_location']] = 0;
        }
        }
        }
        #After Pending


        }

        }
        return '01';
    }
    /*
    *
    * getLocation() function will get an item sku as a parameter.
    * it will check if there is a possibility of grouping the items and assign a location
    *
    */

    private function getLocation($itemSku) {
    // if ($this->getGroupedLocation()) {
    // return $this->_groupedLocation;
    // } 
    }
    /**
     * getSkuInventory() function will check the inventory if a sku non-domestic non-set
     * and return an array of location and quantites
     */
    public function getSkuInventory($itemSku,$qtyOrdered)
    {
        //Quantity of given item in Inventory
        $skuInventoryFromAR = 0;
        //Final Locations with inventory of given item
        $finalLocations = array();
        // Get Encoded json of location and quantity
        $locations = implode(array_keys($this->_allLocationsZipcodes),',');
        $responseString = $this->inventoryLevel($itemSku, $locations);
        $responseInv = json_decode($responseString);
        // Assoc Array of location id => qty of given item 
        $locationsInv = $responseInv->{'LIST'};
        // var_dump($locationsInv);

        //Find pending items number
        $pending = $this->getPendingEstimate("Sample Product");

        if ($pending != 0) {
            foreach ($pending as $valueRunningEstimates) {
                $itemPendingSku = (string)$valueRunningEstimates['sku'];
                $pendingEstimate = intval($valueRunningEstimates['pending']);
                $skuPending = array();
                if(array_key_exists($itemPendingSku, $skuPending)) {
                    $skuPending[$itemPendingSku] += $pendingEstimate;
                }
                else {
                    $skuPending[$itemPendingSku] = $pendingEstimate;
                }
            }
        }
        else {
            $skuPending = null;
        }

        //Iterate through the list of locations

        foreach($locationsInv as $locationInv) {
            if($locationInv->location!=="TOTAL") {
                $skuInventoryFromAr = $locationInv->quantity;


                $inventory = (int)$skuInventoryFromAr - (1+$qtyOrdered) ; //Replace 1 with $skuPending[$itemPendingSku];

                if ($inventory >= $qtyOrdered) { 
                    array_push($finalLocations,$locationInv->location);
                }

            }
            $inventory = 0;
        }


        //If no stock send Update to main HQ
        if(count($finalLocations)>0) 
        {
        //$this->outOfStockNoticfication()
        return '01';
        }
        //Select a random location from final locations
        $finalLocation = array_rand($finalLocations);

        return $finalLocation;
    }

    /**
     * getSetInventory() 
     */
    public function getSetInventory($itemId, $qty_ordered)
    {
        $returnString = $this->GetSetItems($itemId);
        $returnSetItems = json_decode($returnString);
        // return $returnSetItems;
        //Check if correct response is returned
        if($returnSetItems->OK) {
            // iterate through subitems of bundle or grouped product
            foreach($returnSetItems->LIST as $subItem) {
                // print_r($subItem);
                $subItemId = $subItem->ITEM_ID;
            }
        }
    }

    /*
    *
    * getLnt() function is the call to the google api that returns latitude and longitude from zipcode
    *
    */
    private function getLnt($zip)
    {
        $url = "http://maps.googleapis.com/maps/api/geocode/json?address=" . urlencode($zip) . "&sensor=false";
        $result_string = file_get_contents($url);
        $result = json_decode($result_string, true);
        if ($result['status'] != 'OVER_QUERY_LIMIT') {
            $result1[] = $result['results'][0];
            $result2[] = $result1[0]['geometry'];
            $result3[] = $result2[0]['location'];
            return $result3[0];
        }
        else
            return false;
    }

    /**
     * getDistance() function will return the number of miles between two zipcodes
     */
    public function getDistance($zip1, $zip2)
    {
        $first_lat = $this->getLnt($zip1);
        $next_lat = $this->getLnt($zip2);
        if ($first_lat && $next_lat) {
            $lat1 = $first_lat['lat'];
            $lon1 = $first_lat['lng'];
            $lat2 = $next_lat['lat'];
            $lon2 = $next_lat['lng'];
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
        }
        else {
            $miles = 0;
        }

        return $miles;
    }

    /**
    * 
    * 
    * getPendingEstimate() 
    * 
    * 
    */
    public function getPendingEstimate($sku)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance(); // Instance of object manager
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        //Sql Query which takes the pending sku details of order processing
        $sql = "SELECT it.sku, SUM(it.qty_ordered - it.qty_invoiced) as pending
        FROM sales_order_item it
        LEFT JOIN sales_order o
        ON o.entity_id = it.order_id

        WHERE date(it.created_at) > DATE_SUB(Now(),INTERVAL 60 DAY)
        AND o.status != 'complete'
        AND o.status != 'canceled'
        AND o.status != 'closed'
        AND it.sku = '$sku'

        AND (it.qty_ordered - it.qty_invoiced) > 0
        GROUP BY it.sku
        ";
        $result = $connection->fetchAll($sql);
        if (count($result) > 0)
        {
            return $result;
        }
        else
        {
            return 0;
        } 
    }

    /**
     * getDomesticInventory() function will check if the product is available in inventory using API
     */
    public function getDomesticInventory($itemSku, $qtyOrdered)
    {
        //Quantity of given item in Domestic Inventory
        $skuInventoryFromAR = 0;
        // Get Encoded json of location and quantity
        $inventoryLevel = $this->inventoryLevel($itemSku, $this->_domesticLocation);
        if ($inventoryLevel->OK) {
            # code...
            $domesticItemInventory = $inventoryLevel->LIST[0]->quantity;
            $pendingItems = $this->getPendingEstimate($itemSku); # get Pending Items List 
        } else {
            # code...
            throw new Exception("Inventory Level : " . $inventoryLevel->INFO);
        }
        
        // Get qty of given item in domestic location
        $skuInventoryFromAR = $locationInv[0]->quantity;
        $pending = $this->getPendingEstimate($itemSku);
        if ($pending != 0) {
            foreach ($pending as $valueRunningEstimates) {
                $val0 = (string)$valueRunningEstimates['sku'];
                $val1 = intval($valueRunningEstimates['pending']);

                if(array_key_exists ( $val0 , $skuPending )) {
                    $skuPending[$val0] += $val1;
                }
                else {
                    $skuPending[$val0] = $val1;
                }
            }
        }
        else {
            $skuPending = null;
        }
        $inventory = $skuInventoryFromAR - ($skuPending - $qtyOrdered);
        if ($inventory < $qty)
            $inventory = 0;
        return $inventory;
    }


    /**
     * checkDomesticInventory() function will first get inventory for domestic items
     * then it will compare with different location and assign 33 if the destination is greater than 80 miles
     */
    private function checkDomesticInventory($productSku, $qtyOrdered)
    {

        if ($this->getDomesticInventory($productSku, $qtyOrdered) > 0) {
            foreach ($this->_allLocationsZipcodes as $store => $zip) {
                #Incomplete Get zipcode of shipment
                if ($this->getDistance($zip, 686106) < 80)
                    return 06;
            }
            return 33;
        }
        else {
            // $this->sendOutOfStockNotification($sku);
            return 01;
        }
    }

    /**
     * Get Order Info by Order Id
     */
    public function getOrderInfo($orderId)
    {
        return $this->_orderRepository->get($orderId);
    }

    /**
     * Assign a inventory location to order items
     */
    public function assignInventoryLocation($item)
    {
        # Sample Data
        $shippingRate = "international";
        $set = "Y";

        # Setting Up values
        $itemId = $item->getItemId();
        $itemProductSku = $item->getSku();
        $itemQuantity = $item->getQtyOrdered();
        $itemQtyInvoiced = $item->getQtyInvoiced();

        echo $this->getDistance(72201, 85055);
        die();
        /**
         * Get Product Info by Sku
         */
        $product = $this->getProductBySku($itemProductSku);
        /**
         * Get Product Vendor Id
         */
        $vendorId = $product->getVendorId();

        # Check if vendor is Curacao
        if ($vendorId != '2139') {
            //Check if shipping type is 'store pickup'
            # $pickup = strtolower(trim($item->getshippingtype())) == 'storepickup' ? TRUE : FALSE;
            # Dummy Values
            $order = $this->getOrderInfo($item->getOrderId());
            $order->getShippingMethod();
            
            echo $pickup = strtolower(trim($order->getShippingMethod())) == 'storepickup' ? True : False;   # incomplete...
            die();
            
            if ($pickup === True) {
                //Get the pickup location of the item
                $pickupLocationCode = $item->getPickupLocation();   # incomplete...
                return $pickupLocationCode;
            } else {
                if ($shippingRate == "domestic") {
                    $this->checkDomesticInventory($itemProductSku, $itemQuantity);
                    # dummy values
                    $pickupLocationCode = '89';
                } else {
                    if ($set === 'Y') {
                        //virtual product
                        $pickupLocationCode = getSetInventory($itemProductSku, $itemQuantity);
                    } else {
                        # code...
                        //A simple product
                        $pickupLocationCode = $this->getLocation($sku);
                    }
                }
            }
        } else {
            return '33'; 
        }
    }
}