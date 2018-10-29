<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ArInvoice
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
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
     * @var \Magento\Sales\Model\OrderRepository
     **/
    protected $_orderRepository;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $_productRepository;

    /**
     * \Magento\Framework\App\ResourceConnection
     */
    protected $_resourceConnection;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     */
    protected $_customerRepositoryInterface;

    /**
     * Constructor
     *
     * @param \Magento\Sales\Model\OrderRepository $orderRepository
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\App\ResourceConnection $resourceConnection
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     */
    public function __construct(
        \Magento\Sales\Model\OrderRepository $orderRepository,
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\ResourceConnection $resourceConnection,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Dyode\ARWebservice\Helper\Data $arWebServiceHelper,
        \Dyode\AuditLog\Model\ResourceModel\AuditLog $auditLog
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_productRepository = $productRepository;
        $this->_resourceConnection = $resourceConnection;
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->arWebServiceHelper = $arWebServiceHelper;
        $this->auditLog = $auditLog;
    }

    /**
     * Initialize Rest Api Connection
     */
    public function initRestApiConnect($url)
    {
        /**
         * Init Curl
         */
        $baseUrl = $this->arWebServiceHelper->getApiUrl();
        $url = $baseUrl . $url;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        /**
         * Set Content Header
         */
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'X-Api-Key: ' . $this->arWebServiceHelper->getApiKey(),
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
        $url = "CreateRevEstimate";
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

        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id' => "",
            'action' => 'ArInvoice Generation API Response',
            'description' => "input : " . json_encode($inputArray) . "  response : " . $response,
            'client_ip' => "",
            'module_name' => "Dyode_ArInvoice"
        ]);

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
        $url = "webDownpayment";
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

        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id' => "",
            'action' => 'Ar Web Downpayment API Response',
            'description' => "input : " . json_encode($inputArray) . "  response : " . $response,
            'client_ip' => "",
            'module_name' => "Dyode_ArInvoice"
        ]);

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
        $url = "SupplyInvoice";
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
        $url = "InventoryLevel?item_id=$itemId&locations=$locations";
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
     * Get Set Items using API -> GetSetItems
     *
     * @return Array
     */
    public function getSetItems($itemId)
    {
        $url = $this->arWebServiceHelper->getApiUrl() . "getSetItems?item_id=$itemId";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Content-Type: application/json",
            "X-Api-Key: " . $this->arWebServiceHelper->getApiKey()]
        );

        $result = curl_exec($ch);
        curl_close($ch);

        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id' => "",
            'action' => 'AR get set items',
            'description' => "input : itemId : " . $itemId . "  response : " . $result,
            'client_ip' => "",
            'module_name' => "Dyode_ArInvoice"
        ]);
        error_log("input : itemId : " . $itemId . "  response : " . $result);
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
        $url = "AppleCareListWarranties";
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
     * Apple Care Set Warranty
     * 
     * @return Array
     */
    public function appleCareSetWarranty($inputArray)
    {
        /**
         * Initialize Rest Api Connection
         */
        $url = "AppleCareSetWarranty";
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

        //logging audit log
        $this->auditLog->saveAuditLog([
            'user_id' => "",
            'action' => 'Apple care warranty',
            'description' => "input : " . json_encode($inputArray) . "  response : " . $response,
            'client_ip' => "",
            'module_name' => "Dyode_ArInvoice"
        ]);

        return json_decode($response);
    }

    /**
     * Load product by Product Id
     * 
     * @return Object
     */
    private function getProductById($id)
    {
        return $this->_productRepository->getById($id);
    }

    /**
     * Load product by SKU
     * 
     * @return Object
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
     * Get the inventory details of the product
     *
     * @param $productId
     * @return array
     */
    public function getProductInventory($productId)
    {
        $resourceConnection = $this->_resourceConnection->getConnection();
        $query = "SELECT `finalinventory` FROM `location_inventory` WHERE `productid` = $productId";
        $result = $resourceConnection->fetchAll($query);
        return $result;
    }

    /**
     * Assign a inventory location to order items
     *
     * @return Array
     */
    public function assignInventoryLocation($item)
    {
        /**
         * Get item qty ordered
         */
        $itemQty = $item->getQtyOrdered();
        /**
         * Get item Product Id
         */
        $productId = $item->getProductId();
        /**
         * Get Product Info by Sku
         */
        $product = $this->getProductById($productId);
        /**
         * Get item Sku
         */
        $itemSku = $product->getSku();
        /**
         * Get Product Vendor Id
         */
        $vendorId = $product->getData('vendorid');
        /**
         * Getting the Inventory Level from location_inventory table
         */
        $result = $this->getProductInventory($productId);

        $inventoryLocations = (!empty($result)) ? json_decode($result[0]['finalinventory']) : [];

        if ($vendorId != '2139') {  # If the vendor is not Curacao
            return '33';
        } else {

            $order = $this->getOrderInfo($item->getOrderId());
            $storePickup = $item->getData('delivery_type');

            if ($storePickup == True) { # If the Delivery Type is Store Pickup
                $storeLocationCode = $item->getData('store_location');
                return $storeLocationCode;
            } else { # If the Delivery Type is Shipping
                $shippingRate = $product->getData('shiptype');
                $shippingZipCode = $order->getShippingAddress()->getPostCode();

                if ($shippingRate == "Domestic") {
                    if (array_key_exists($this->_domesticLocation, $inventoryLocations)) {
                        $domesticItemInventory = $inventoryLocations[$this->_domesticLocation];
                        $storeLocationCode = $this->getDomesticInventoryLocation($itemSku, $itemQty, $shippingZipCode, $domesticItemInventory);
                    } else {
                        # Send Out of Stock Notification
                        $storeLocationCode = 01;
                    }
                    return $storeLocationCode;
                } else {
                    $set = $product->getData('set');

                    if ($set == "1") {
                        $setItems = json_decode($this->getSetItems($itemSku));
                        $pendingArray = $this->getPendingEstimate($itemSku);
                        if (count($pendingArray) > 0) {
                            $pendingValue = $pendingArray[0]['pending'];
                        } else {
                            $pendingValue = 0;
                        }
                        $availableInventory = array();

                        if ($setItems->OK) {
                            $itemsArray = array();
                            $setItemsQty = array();
                            $pending = array();
                            foreach ($setItems->LIST as $setItem) {
                                array_push($itemsArray, $setItem->ITEM_ID);
                                $setItemsQty[$setItem->ITEM_ID] = $setItem->QTY;
                                $pending[$setItem->ITEM_ID] = $pendingValue * $setItemsQty[$setItem->ITEM_ID];

                                $setItemProduct = $this->getProductBySku($setItem->ITEM_ID);

                                $resultSetItem = $this->getProductInventory($setItemProduct->getId());

                                $setItemInventoryLevel = (!empty($resultSetItem)) ?
                                    json_decode($resultSetItem[0]['finalinventory']) : [];
                                if (!empty($setItemInventoryLevel)) {
                                    foreach ($setItemInventoryLevel as $key => $value) {
                                        $stockAvailable = $value - $pending[$setItem->ITEM_ID];
                                        $stockOrdered = $itemQty * $setItemsQty[$setItem->ITEM_ID];
                                        if (empty($availableInventory[$key])) {
                                            $availableInventory[$key] = array();
                                        }
                                        if ($stockAvailable > $stockOrdered) {
                                            array_push($availableInventory[$key], $setItem->ITEM_ID);
                                        }
                                    }
                                }
                            }

                        }
                        $setLocationFound = 0;
                        foreach ($availableInventory as $locations => $items) {
                            if (count($itemsArray) == count($items)) {
                                $setLocationFound = 1;
                                return $locations;
                            }
                        }
                        if ($setLocationFound != 1) {
                            # Send Out of Stock Notification
                            return 01;
                        }
                    } else {
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
        $connection = $this->_resourceConnection->getConnection();
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
        return $result;
    }

    /**
     * Get Domestic Items Inventory Location
     *
     * @return Integer
     */
    public function getDomesticInventoryLocation($productSku, $qtyOrdered, $shippingZipCode, $domesticItemInventory)
    {
        if ($domesticItemInventory >= $qtyOrdered) {
            $resourceConnection = $this->_resourceConnection->getConnection();
            $query = "SELECT * FROM `locations` WHERE `zip` = $shippingZipCode";
            $result = $resourceConnection->fetchAll($query);
            if ($result[0]['lat'] and $result[0]['lng']) {
                $shippingZipCodeLat = $result[0]['lat'];
                $shippingZipCodeLng = $result[0]['lng'];
            } else {
                throw new \Exception("Error Finding ZipCode Coordinates", 1);
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
            # Send the Out of Stock Notification
            return $storeLocationCode = 01;
        }
    }

    /**
     * Get Grouped Items Location
     *
     * @return Array
     */
    public function getGroupedLocation($order, $orderItems)
    {
        $groupedLocationFound = 0;
        $availableLocations = array();

        foreach ($orderItems as $itemId => $productInfo) {
            $resultSetItem = $this->getProductInventory($productInfo['ProductId']);

            $inventoryLevel = (!empty($resultSetItem)) ? json_decode($resultSetItem[0]['finalinventory']) : [];

            foreach ($inventoryLevel as $key => $value) {
                // Array Initializing
                if (empty($availableLocations[$key])) {
                    $availableLocations[$key] = array();
                }
                if ($value > $productInfo['ItemQty']) {
                    array_push($availableLocations[$key], $itemId);
                }
            }
        }

        foreach ($availableLocations as $location => $items) {
            if (count($items) == count($orderItems)) {
                foreach ($items as $itemId) {
                    $groupedItemsLocation[$itemId] = $location;
                }
                $groupedLocationFound = 1;
                return $groupedItemsLocation;
            }
        }

        if ($groupedLocationFound != 1) {
            $previous = array();
            foreach ($orderItems as $itemId => $productInfo) {
                if (isset($previous)) {
                    $foundInPrevious = 0;
                    foreach ($previous as $location) {
                        if (in_array($itemId, $availableLocations[$location])) {
                            $groupedItemsLocation[$itemId] = $location;
                            $foundInPrevious = 1;
                            break;
                        }
                    }
                    if ($foundInPrevious == 1) {
                        continue;
                    }
                }
                foreach ($availableLocations as $location => $value) {
                    if (in_array($itemId, $availableLocations[$location])) {
                        $groupedItemsLocation[$itemId] = $location;
                        array_push($previous, $location);
                        break;
                    }
                }
            }
        }
        ksort($groupedItemsLocation);

        return $groupedItemsLocation;
    }

    /**
     * Link Apple Care Warranty
     *
     * @return Void
     */
    public function linkAppleCare($order)
    {
        $writer = new \Zend\Log\Writer\Stream(BP . "/var/log/linkapplecare.log");
		$logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);

        $appleItems = array();
        $itemsArray = array();
        $toLinkItems = array();
        $accountNumber = "";
        $firstName = (!empty($order->getCustomerFirstName())) ? $order->getCustomerFirstName() : $order->getBillingAddress()->getFirstName();
        $lastName = (!empty($order->getCustomerLastName())) ? $order->getCustomerLastName() : $order->getBillingAddress()->getFirstName();
        $email = $order->getCustomerEmail();
        $telephone = $order->getBillingAddress()->getTelephone();
        $customerId = $order->getCustomerId();

        if (!empty($customerId)) {
            $customer = $this->_customerRepositoryInterface->getById($customerId);
            $curacaoCustId = $customer->getCustomAttribute("curacaocustid");
            if (!empty($curacaoCustId)) {
                $accountNumber = $curacaoCustId->getValue();
            }
        } else {
            if ((strpos($order->getPayment()->getMethod(), 'authorizenet')) &&
                ($order->getPayment()->getAmountAuthorized() == $order->getGrandTotal())) {
                $accountNumber = '500-8555';
            }
        }

        $invoiceNumber = $order->getData('estimatenumber');
        if (empty($invoiceNumber)) {
			$logger->info("Order Id : " . $order->getIncrementId());
			$logger->info("Invoice Number Not found ");
		} else {
            foreach ($order->getAllItems() as $orderItem) {
                $product = $this->_productRepository->getById($orderItem->getProductId());
                $brand = $product->getResource()->getAttribute('tv_brand')->getFrontend()->getValue($product);
                if ($brand != 'Apple') {
                    array_push($appleItems, $orderItem);
                    $itemsArray[$orderItem->getId()] = $product->getSku();
                }
            }

            if (count($appleItems) > 1) {
                foreach ($appleItems as $appleItem) {
                    if ($appleItem->getProductType() != 'virtual') {
                        if ($appleItem->getData('warranty_parent_item_id')) {
                            $productToLink = $itemsArray[$appleItem->getData('warranty_parent_item_id')];
                            $warrantyToLink = $appleItem->getSku();
                            array_push($toLinkItems, array($productToLink, $warrantyToLink));
                        }
                    }
                }
            }

            if (!empty($toLinkItems)) {
                // Setting up input values
                $inputArray = array(
                    "invoice" => (string)$invoiceNumber,
                    "cust_id" => $accountNumber,
                    "f_name" => $firstName,
                    "l_name" => $lastName,
                    "email" => $email,
                    "cell_no" => $telephone,
                );
                $inputArray["items"] = $toLinkItems;

                $response = $this->appleCareSetWarranty($inputArray);
            }

            if (empty($response)) {

                //logging audit log
                $this->auditLog->saveAuditLog([
                    'user_id' => $accountNumber,
                    'action' => 'Apple Care Warranty linking',
                    'description' => "No response",
                    'client_ip' => "",
                    'module_name' => "Dyode_ArInvoice"
                ]);

                $logger->info("Order Id : " . $order->getIncrementId());
                $logger->info("API Response not Found.");
            } else if ($response->OK = true) {
                //logging audit log
                $this->auditLog->saveAuditLog([
                    'user_id' => $accountNumber,
                    'action' => 'Apple Care Warranty linked successfully',
                    'description' => $response->INFO,
                    'client_ip' => "",
                    'module_name' => "Dyode_ArInvoice"
                ]);

                $logger->info("Order Id : " . $order->getIncrementId());
                $logger->info($response->INFO);

                return true;
            } else {
                $this->auditLog->saveAuditLog([
                    'user_id' => $accountNumber,
                    'action' => 'Fail to link Apple Care Warranty',
                    'description' => $response->INFO,
                    'client_ip' => "",
                    'module_name' => "Dyode_ArInvoice"
                ]);

                $logger->info("Order Id : " . $order->getIncrementId());
                $logger->info($response->INFO);

                return false;
            }
        }
    }
}