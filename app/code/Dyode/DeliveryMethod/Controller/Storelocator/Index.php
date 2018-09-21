<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       09/09/2018
 */

namespace Dyode\DeliveryMethod\Controller\Storelocator;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action {

  protected $_customerSession;
  protected $_checkoutSession;
  protected $_coreSession;
  protected $_messageManager;
//  protected $_storeloc;
  /**
   * Constructor
   *
   * @param \Magento\Framework\App\Action\Context  $context
   * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
   */
  public function __construct(
      Context $context,
      \Magento\Checkout\Model\Session $checkoutSession,
      \Magento\Framework\Session\SessionManagerInterface $coreSession,
      \Magento\Framework\Message\ManagerInterface $messageManager,
      \Magento\Customer\Model\Session $customerSession
    //  \Dyode\DeliveryMethod\Model\StorePickup $storeloc
  ) {
      parent::__construct($context);
      $this->_customerSession = $customerSession;
      $this->_coreSession = $coreSession;
      $this->_messageManager = $messageManager;
      $this->_checkoutSession = $checkoutSession;
    //  $this->_storeloc = $storeloc;
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
        //  $result = $this->_storeloc()->selection($zipcode);
          $result = $this->getDirection($zipcode);
          echo json_encode($result);
      endif;
  }
  public function getDirection($zipcode){
    $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
    $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
    $connection = $resource->getConnection();
    $tableName = $resource->getTableName('locations');

    //Select Data from table
    $sql = "Select * FROM " . $tableName . " WHERE zip =".$zipcode;
    $result = $connection->fetchAll($sql);
    return $result;
  }
}
?>
