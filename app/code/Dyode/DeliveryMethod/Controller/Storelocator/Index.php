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

class Index extends Action {

  protected $_customerSession;
  protected $_checkoutSession;
  protected $_coreSession;
  protected $_messageManager;
  protected $_distHelper;
  protected $_locationJson;
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
  ) {
      parent::__construct($context);
      $this->_customerSession = $customerSession;
      $this->_coreSession = $coreSession;
      $this->_messageManager = $messageManager;
      $this->_checkoutSession = $checkoutSession;
      $this->_distHelper = $distHelper;
      $this->_locationJson = $locationJson;
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
          $result = $this->getStores($zipcode);
      //    $result = $this->_locationJson->getCollection();
          echo json_encode($result);
      endif;
  }
  public function getStores($zipcode){
    $storeList = array();
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
    $tableName = $resource->getTableName('locations');

    //Select Data from table
    $sql = "Select lat,lng FROM " . $tableName . " WHERE zip =".$zipcode;
    $zipResult = $connection->fetchAll($sql);
    foreach($zipResult as $key=>$value){
       $lat1 = $value['lat'];
       $lng1 = $value['lng'];
    }
    $storelocTable = $resource->getTableName('aw_storelocator_location');
    //Select Data from table
    $sql = "Select location_id,title,city,street,country_id,zip,latitude,longitude,phone FROM " . $storelocTable;
    $result = $connection->fetchAll($sql);

    if(isset($lat1) && isset($lng1)){
      foreach($result as $key=>$value){
          $distance = $this->_distHelper->getDistance( $lat1,$lng1,$value['latitude'],$value['longitude']);
          $value['distance'] = $distance;
          $storeList[] = $value;
      }
      //Sorting with the distance
      ksort($storeList);

    } else{
      foreach($result as $key=>$value){
          $storeList[] = $value;
      }
    }
    return $storeList;

  }

  public function selectStore(){

  }

}
?>
