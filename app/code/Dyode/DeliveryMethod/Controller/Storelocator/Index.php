<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       09/09/2018
 */

namespace Dyode\DeliveryMethod\Controller\Storelocator;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Aheadworks\StoreLocator\Model\ResourceModel\LocationStore;
use Magento\Framework\ObjectManagerInterface;

class Index extends Action {

  protected $_customerSession;
  protected $_checkoutSession;
  protected $_coreSession;
  protected $_messageManager;
  protected $_distHelper;
  protected $_locationJson;
  protected $_objectManager;
  /**
   * Constructor
   *
   * @param \Magento\Framework\App\Action\Context  $context
   * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
   */
  public function __construct(
      Context $context,
      LocationStore $locationJson,
      \Magento\Checkout\Model\Session $checkoutSession,
      \Magento\Framework\Session\SessionManagerInterface $coreSession,
      \Magento\Framework\Message\ManagerInterface $messageManager,
      \Dyode\ArInvoice\Helper\Data $distHelper,
      \Magento\Customer\Model\Session $customerSession
    //  ObjectManagerInterface $objectManager
  ) {
      parent::__construct($context);
      $this->_customerSession = $customerSession;
      $this->_coreSession = $coreSession;
      $this->_messageManager = $messageManager;
      $this->_checkoutSession = $checkoutSession;
      $this->_distHelper = $distHelper;
      $this->_locationJson = $locationJson;
    //  $this->_objectManager = $objectManager;
  }
  /**
   * Execute view action
   *
   * @return \Magento\Framework\Controller\ResultInterface
   */
  public function execute()
  {
      if ($this->getRequest()->getPost('zipcode')):
          $zipcode = $this->getRequest()->getPost('zipcode');
          $pid = $this->getRequest()->getPost('pid');
          $result = $this->getStores($zipcode,$pid);
      //    $result = $this->_locationJson->getCollection();
          echo json_encode($result);
      endif;
  }
  public function getStores($zipcode,$itemId){
    $storeList = array();
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
    $media_url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
    $tableName = $resource->getTableName('locations');
    $havedistance = 0;

    //Select Data from table
    $sql = "Select lat,lng FROM " . $tableName . " WHERE zip =".$zipcode;
    $zipResult = $connection->fetchAll($sql);
    foreach($zipResult as $key=>$value){
       $lat1 = $value['lat'];
       $lng1 = $value['lng'];
    }
    $storelocTable = $resource->getTableName('aw_storelocator_location');
    //Select Data from table
    $sql = "Select location_id,title,city,street,country_id,zip,latitude,longitude,phone,image FROM " . $storelocTable;
    $result = $connection->fetchAll($sql);

    if(isset($lat1) && isset($lng1)){
      $havedistance =1;
      foreach($result as $key=>$value){
          $distance = $this->_distHelper->getDistance( $lat1,$lng1,$value['latitude'],$value['longitude']);
          $value['distance'] = $distance;
          $storeList[] = $value;
      }
      //Sorting with the distance
      ksort($storeList);

      //create the html
      $result ='';
      //  $media_dir = $this->getMediaUrl();
      foreach ($storeList as $key => $value) {
          $imageUrl = 'aheadworks/store_locator/'.$value['image'];
          $result .= '<div class="avail-store-item">
                <div class="avail-store-image">
                  <img src="'.$media_url.$imageUrl.'"/>';
          $result .= '<p class="store-distance">'.round($value['distance']).'mi</p>';
          $result .= '</div>';
          $result .= '<div class="avail-store-details">
                  <ul>
                  <li class="store-location">'.$value['title'].'</li>
                  <li>'.$value['city'].'</li>
                  <li>'.$value['street'].','.$value['country_id'].','.$value['zip'].'</li>
                  <li>'.$value['phone'].'</li>';
          $locAttr = "attr: {id:'location_id_".$value['location_id']."',name:'location_id_".$value['location_id']."'},value:'".$value['location_id']."-".$itemId."'";
          $itemAttr = "attr: {id:'item_id_".$value['location_id']."',name:'item_id".$value['location_id']."'},value:'".$itemId."'";
          $lang = "i18n: 'Select'";
          $result .='<form class="form" id="storeselection'.$value['location_id'].'">
                      <input type="hidden" data-bind="'.$locAttr.'"/>
                      <button type="button" class="action" data-bind="event:{click:updateLocation.bind(this,event)}" >
                         <span data-bind="'.$lang.'"></span>
                      </button>
                  </form>';
              $result .= ' </ul> </div> </div>';

      }
      return $result;
    //  return $storeList;
    } else{
      return "<p>Please enter valid zipcode</p>";
    }

  }

}
?>
