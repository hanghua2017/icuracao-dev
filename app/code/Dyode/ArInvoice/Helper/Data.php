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
     * @var \Magento\Sales\Model\OrderRepository
     **/
    protected $_orderRepository;

    /**
     * \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

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
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection
    ) {
        $this->_orderRepository = $orderRepository;
        $this->jsonHelper = $jsonHelper;
        $this->_productRepository = $productRepository;
        $this->_resourceConnection = $resourceConnection;
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
     * Inventory Level using API -> InventoryLevel
     * 
     * @return Array
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
        curl_close($ch);
        return $result;
    }

    /**
     * Apple Care List Warranties
     * 
     * @return Array
     */
    public function appleCareListWarranties()
    {
        /**
         * Initialize Rest Api Connection
         */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/AppleCareListWarranties";
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
        // return $response;
    }

    /**
     * Apple Care Set Warranty
     * 
     * @return Array
     */
    public function appleCareSetWarranty($inputArray)
    {
        /**
         * Initialize Rest Api Connection
         */
        $url = "https://exchangeweb.lacuracao.com:2007/ws1/test/restapi/ecommerce/AppleCareSetWarranty";
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
        // return $response;
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
     * Get Order Info by Order Id
     *
     * @return Object
     */
    public function getOrderInfo($orderId)
    {
        return $this->_orderRepository->get($orderId);
    }

    /**
     * Assign a inventory location to order items
     *
     * @return Array
     */
    public function assignInventoryLocation($item)
    {
        # Sample Data
        $shippingRate = "international";
        $set = "Y";

        # Setting Up values
        $itemId = $item->getItemId();
        $itemSku = $item->getSku();
        $itemQty = $item->getQtyOrdered();
        $itemQtyInvoiced = $item->getQtyInvoiced();
        $productId = $item->getProductId();

        /**
         * Get Product Info by Sku
         */
        $product = $this->getProductById($productId);
        /**
         * Get Product Vendor Id
         */
        $vendorId = $product->getVendorId();
        /**
         * Getting the Inventory Level from Product Attribute
         */
        if (!empty($product->getInventoryLevel())) {
            # code...
            $inventoryLevel = explode(", ", $product->getInventoryLevel());
            $inventoryLocations =  array();
            foreach ($inventoryLevel as $value) {
                # code...
                $inventoryLocations[explode(":", $value)[0]] = explode(":", $value)[1];
            }
            unset($inventoryLevel);
        } else {
            # code...
            throw new Exception("Product Inventory Level Not Found", 1);
        }
        if ($vendorId != '2139') {  # If the vendor is not Curacao
            return '33';
        } else { # If the vendor is Curacao
            # Get Order Details
            $order = $this->getOrderInfo($item->getOrderId());
            # Get Delivery Method
            $storePickup = strtolower(trim($order->getShippingMethod())) == 'storepickup' ? True : False;   # incomplete...
            if ($storePickup == True) { # If the Delivery Type is Store Pickup
                # incomplete...
                $storeLocationCode = $item->getPickupLocation();
                return $storeLocationCode;
            } else { # If the Delivery Type is Shipping
                $shippingRate = $product->getShippingRate();
                $shippingZipCode = $order->getShippingAddress()->getPostCode();
                if ($shippingRate == "Domestic") {
                    # dummy values...
                    $itemSku = '32A-061-101946';
                    $shippingZipCode = 35801;
                    if (array_key_exists($this->_domesticLocation, $inventoryLocations)) {
                        # code...
                        $domesticItemInventory = $inventoryLocations[$this->_domesticLocation];
                        $storeLocationCode = $this->getDomesticInventoryLocation($itemSku, $itemQty, $shippingZipCode, $domesticItemInventory);
                    } else {
                        # Send Out of Stock Notification
                        $storeLocationCode = 01;
                    }
                    return $storeLocationCode;
                } else {
                    $set = $product->getIsSet();
                    if ($set == 'Yes') {
                        $itemSku = "17D-868-DSCW610PAK1";
                        $setItems = json_decode($this->getSetItems($itemSku));
                        // echo $itemSku;
                        echo "<pre>";

                        // $magentoSkuArray = array('Test', 'Test2');
                        // $pendingArray = $this->getPendingEstimate($value);
                        // $pending = $pendingArray[0]['pending'];
                        $pending = 5;

                        echo "</pre>";
                        $availableInventory = array();
                        // die();
                        if ($setItems->OK) {
                            # code...
                            $itemsArray = array();
                            $setItemsQty = array();
                            $pending = array();
                            foreach ($setItems->LIST as $setItem) {
                                # code...
                                // $itemsArray = $setItem->ITEM_ID;
                                array_push($itemsArray, $setItem->ITEM_ID);
                                $setItemsQty[$setItem->ITEM_ID] = $setItem->QTY;
                                $pending[$setItem->ITEM_ID] = 5 * $setItemsQty[$setItem->ITEM_ID];
                                echo "<pre>";
                                $setItem->ITEM_ID = 'Test';
                                // $magentoSkuArray = array('Test', 'Test2', 'Bundle1-Test-Test2');
                                // $pendingArray = $this->getPendingEstimate($setItem->ITEM_ID);
                                // // $pending[$pendingArray[0]['pending']);
                                // $pending[$setItem->ITEM_ID] = $pendingArray[0]['pending'];
                                $setItemProduct = $this->getProductBySku('Test');
                                if (!empty($setItemProduct->getInventoryLevel())) {
                                    # code...
                                    $setItemInventoryLevel = explode(", ", $setItemProduct->getInventoryLevel());
                                    $setItemInventoryLocations =  array();
                                    foreach ($setItemInventoryLevel as $value) {
                                        # code...
                                        $stockAvailable = (int)explode(":", $value)[1] - $pending[$setItem->ITEM_ID];
                                        $stockOrdered = $itemQty * $setItemsQty[$setItem->ITEM_ID];
                                        if (empty($availableInventory[explode(":", $value)[0]])) {
                                            # code...
                                            $availableInventory[explode(":", $value)[0]] = array();
                                        }
                                        if ($stockAvailable > $stockOrdered) {
                                            # code...
                                            array_push($availableInventory[explode(":", $value)[0]], $setItem->ITEM_ID);
                                        }
                                    }
                                    unset($setItemInventoryLevel);
                                } else {
                                    # code...
                                    throw new Exception("Product Inventory Level Not Found", 1);
                                }
                                print_r($setItemInventoryLocations);
                            }
                        } else {
                            # code...
                            throw new Exception("Set Items Not Found", 1);
                        }
                        // print_r($itemsArray);
                        $setLocationFound = 0;
                        foreach ($availableInventory as $locations => $items) {
                            # code...
                            if (count($itemsArray) ==  count($items)) {
                                # code...
                                $setLocationFound = 1;
                                return $locations;
                            }
                        }
                        // print_r($availableInventory);
                        // print_r($setItemsQty);
                        // echo "</pre>";
                        // die();
                        if ($setLocationFound == 1) {
                            # code...
                            # Send Out of Stock Notification
                            return 01;
                        }
                    } else {
                        # code...
                        return "k";
                    }
                }
            }
        }
    }

    /**
     * Get Distance in miles between two zipcodes in US
     *
     * @return Float
     */
    public function getDistance($lat1, $lng1, $lat2, $lng2)
    {
        $theta = $lng1 - $lng2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        return $miles;
    }

    /**
     * Get Pending Estimate of Item by Sku
     *
     * @return Array
     */
    public function getPendingEstimate($itemSku)
    {
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
        $connection = $resource->getConnection();

        # Sql Query which takes the pending sku details of order processing
        $sql = "SELECT it.sku, SUM(it.qty_ordered - it.qty_invoiced) as pending
            FROM sales_order_item it
            LEFT JOIN sales_order o
            ON o.entity_id = it.order_id

            WHERE date(it.created_at) > DATE_SUB(Now(),INTERVAL 60 DAY)
            AND o.status != 'complete'
            AND o.status != 'canceled'
            AND o.status != 'closed'
            AND it.sku = '$itemSku'

            AND (it.qty_ordered - it.qty_invoiced) > 0
            GROUP BY it.sku";

        $result = $connection->fetchAll($sql);
        if (count($result) > 0) {
            return $result;
        } else {
            return 0;
        }
    }

    /**
     * Get Domestic Items Inventory Location
     *
     * @return Integer
     */
    public function getDomesticInventoryLocation($productSku, $qtyOrdered, $shippingZipCode, $domesticItemInventory)
    {
        if ($domesticItemInventory >= $qtyOrdered) {
            # code...
            $resourceConnection = $this->_resourceConnection->getConnection();
            $query = "SELECT * FROM `locations` WHERE `zip` = $shippingZipCode";
            $result = $resourceConnection->fetchAll($query);
            if ($result[0]['lat'] and $result[0]['lng']) {
                $shippingZipCodeLat = $result[0]['lat'];
                $shippingZipCodeLng = $result[0]['lng'];
            } else {
                throw new Exception("Error Finding ZipCode Coordinates", 1);
            }
            foreach ($this->_allLocationsZipcodes as $locationCode => $zipCode) {
                $query = "SELECT * FROM `locations` WHERE `zip` = $zipCode ";
                $result = $resourceConnection->fetchAll($query);
                if ($result) {
                    $storeZipCodeLat = $result[0]['lat'];
                    $storeZipCodeLng = $result[0]['lng'];
                    $distance = $this->getDistance($shippingZipCodeLat, $shippingZipCodeLng, $storeZipCodeLat, $storeZipCodeLng);
                    if (round($distance) <= 80) {
                        return $storeLocationCode = 06;
                    }
                }
            }
            return $storeLocationCode = 33;
        } else {
            # code...
            # Send the Out of Stock Notification
            return $storeLocationCode = 01;
        }
    }

    /**
     * Get Grouped Items Location
     *
     * @return Array
     */
    public function getGroupedLocation($order,$orderItems)
    {
        $groupedLocationFound = 0;
        $availableLocations = array();
        $groupedLocation = array();
        // $availableLocationsInv = array();
        // echo count($orderItems);
        // die();
        foreach ($orderItems as $itemId => $productInfo) {
            # code...
            $product = $this->getProductById($productInfo['ProductId']);
            $inventoryLevel = explode(", ",$product->getInventoryLevel());
            // $availableLocationsInv[$itemId] = array();
            $locations = array();
            foreach ($inventoryLevel as $inventoryLoc) {
                # code...
                if (empty($availableLocations[explode(":", $inventoryLoc)[0]])) {
                    # code...
                    $availableLocations[explode(":", $inventoryLoc)[0]] = array();
                }
                if (explode(":", $inventoryLoc)[1] > $productInfo['ItemQty']) {
                    # code...
                    array_push($availableLocations[explode(":", $inventoryLoc)[0]], $itemId);
                    // array_push($locations, explode(":", $inventoryLoc)[0]);
                }
            }
            // $availableLocationsInv[$itemId] = $locations;
        }

        // echo "<pre>";
        // print_r($availableLocations);
        // echo "</pre>";
        // die();
        foreach ($availableLocations as $location => $items) {
            # code...
            if (count($items) == count($orderItems)) {
                # code...
                foreach ($items as $itemId) {
                    # code...
                    $groupedItemsLocation[$itemId] = $location;
                }
                // echo "<pre>";
                // print_r($groupedItemsLocation);
                // echo "</pre>";
                $groupedLocationFound = 1;
                // break;
                return $groupedItemsLocation;
            }
        }
        # dummy values...
        // $orderItems = array(
        //     '133' => array(
        //         'ProductId' => 5,
        //         'ItemQty' => 1.0000
        //     ),
        //     '134' => array(
        //         'ProductId' => 4,
        //         'ItemQty' => 1.0000
        //     ),
        //     '135' => array(
        //         'ProductId' => 3,
        //         'ItemQty' => 1.0000
        //     ),
        //     '136' => array(
        //         'ProductId' => 2,
        //         'ItemQty' => 1.0000
        //     ),
        //     '137' => array(
        //         'ProductId' => 1,
        //         'ItemQty' => 1.0000
        //     )
        // );
        // $groupedLocationFound = 0;

        // $availableLocations = array(
        //     '01' => array(
        //         133,
        //         136,
        //         137
        //     ),
        //     '09' => array(
        //         133,
        //         134,
        //         136,
        //         137
        //     ),
        //     '16' => array(
        //         133,
        //         134,
        //         137
        //     ),
        //     '21' => array(
        //         134,
        //         135,
        //         136
        //     )
        // );
        // echo "<pre>";
        // print_r($orderItems);
        // print_r($groupedLocationFound);
        // print_r($availableLocations);
        // echo "</pre>";
        // die();
        // $orderItems =
        if ($groupedLocationFound != 1) {
            # code...
            $previous = array();
            foreach ($orderItems as $itemId => $productInfo) {
                #code...
                if (isset($previous)) {
                    # code...
                    $foundInPrevious = 0;
                    foreach ($previous as $location) {
                        # code...
                        if (in_array($itemId, $availableLocations[$location])) {
                            # code...
                            $groupedItemsLocation[$itemId] = $location;
                            $foundInPrevious = 1;
                            break;
                        }
                    }
                    if ($foundInPrevious == 1) {
                        # code...
                        continue;
                    }
                }
                foreach ($availableLocations as $location => $value) {
                    # code...
                    if (in_array($itemId, $availableLocations[$location])) {
                        # code...
                        $groupedItemsLocation[$itemId] = $location;
                        array_push($previous, $location);
                        break;
                    }
                }
            }
        }
        // echo "<pre>";
        ksort($groupedItemsLocation);
        // print_r($groupedItemsLocation);
        // echo "</pre>";
        // $availableLocationsInv = array();
        // foreach ($availableLocations as $location => $items) {
        //     # code...
        //     foreach ($items as $key => $itemId) {
        //         # code...
        //         if (empty($availableLocationsInv[$itemId])) {
        //             # code...
        //             $availableLocationsInv[$itemId] = array();
        //         }
        //         // $availableLocationsInv[$itemId] = $location;
        //         array_push($availableLocationsInv[$itemId], $location);
        //     }
        // }
        // echo "<pre>";
        // print_r($availableLocationsInv);
        // echo "</pre>";
        // echo "<pre>";
        // print_r($groupedItemsLocation);
        // echo "</pre>";
        // die();
        return $groupedItemsLocation;
    }
}