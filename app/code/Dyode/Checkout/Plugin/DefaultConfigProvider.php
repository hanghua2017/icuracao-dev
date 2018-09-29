<?php
/**
 * @package   Dyode
 * @author    Mathew Joseph
 */
namespace Dyode\Checkout\Plugin;

class DefaultConfigProvider
{
  
  /**
     * Zipcodes of all inventory locations of Curacao
  */
  private $_allLocationsZipcodes = array('01' => 90015, '09' => 91402, '16' => 90280, '22' => 90255, '29' => 92408, '33' => 90280, '35' => 92708, '38' => 91710, '51' => 92801, '40' => 85033, '57' => 85713, '64' => 89107);
  
  /**
  * @var \Dyode\ArInvoice\Helper\Data
  */
  protected $_distHelper;

  /**
  * Constructor
  *
  * @param \Dyode\ArInvoice\Helper\Data $distHelper
  * 
  */
  public function __construct(\Dyode\ArInvoice\Helper\Data $distHelper)
  { 
    $this->_distHelper = $distHelper;       
  }
  
  public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $subject, $result)
  {
    foreach ($result['quoteItemData'] as $index => $quoteItem) {
      // Check if quote item is freight product
      if($quoteItem['product']['isfreight']) {
        // $result['quoteItemData'][$index]['shippingData'] = $this->collectProductShippingMethods($quoteItem);
        // Check if distance is greater than 80 
        # Get Shipment Address
        // if($this->isDomestic($storeZipCodeLat, $storeZipCodeLng)) {
        //   #If Domestic use ADS Momentum as shipping method
        //   $result['quoteItemData'][$index]['shippingmethod'] = "ADS";
        // }
        // else {
        //   #If Not Domestic use Pilot as shipping method
        //   $result['quoteItemData'][$index]['shippingmethod'] = "Pilot";
        // }
        
      } 
      else {
        // USPS 
        $result['quoteItemData'][$index]['shippingmethod'] = "USPS";
      }

    return $result;
    }
  }

  // Select Shipping Method
  public function collectProductShippingMethods($quoteItem)
  {
    return 'UPS';
  }

  /**
   * 
   * Check if distance to customer address is less than 80 for any store location 
   * 
   */
  public function isDomestic($shipZipCodeLat, $shipZipCodeLng) {
    foreach ($this->_allLocationsZipcodes as $locationCode => $zipCode) {
      $query = "SELECT * FROM `locations` WHERE `zip` = $zipCode ";
      $result = $resourceConnection->fetchAll($query);
      if ($result) {
          $storeZipCodeLat = $result[0]['lat'];
          $storeZipCodeLng = $result[0]['lng'];
          $distance = $this->_distHelper->getDistance($shippingZipCodeLat, $shippingZipCodeLng, $storeZipCodeLat, $storeZipCodeLng);
          if (round($distance) <= 80) {
              return true;
          }
      }
    }
  }

}
?>
