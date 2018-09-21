<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       12/09/2018
 */

namespace Dyode\DeliveryMethod\Controller\Deliverylocation;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;

class Index extends Action {

  protected $_checkoutSession;
  protected $_quoteItem;
  protected $_quoteRepo;
  private $quoteItemFactory;
  private $itemResourceModel;

  public function __construct(
      Context $context,
      \Magento\Checkout\Model\Session $checkoutSession,
      \Magento\Quote\Model\Quote\Item $quoteItem,
      \Magento\Quote\Model\QuoteRepository $quoteRepo,
      \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
      \Magento\Quote\Model\ResourceModel\Quote\Item $itemResourceModel
  ) {
      parent::__construct($context);
      $this->_checkoutSession = $checkoutSession;
      $this->_quoteItem = $quoteItem;
      $this->_quoteRepo = $quoteRepo;
      $this->quoteItemFactory = $quoteItemFactory;
      $this->itemResourceModel = $itemResourceModel;
  }

  /**
   * Execute view action
   *
   * @return \Magento\Framework\Controller\ResultInterface
   */
  public function execute()
  {
      // print_r($this->_checkoutSession->getQuote());
      // echo $this->_checkoutSession->getQuote()->getId();
      if ($this->getRequest()->getPost('location_id')):
          $locationId = $this->getRequest()->getPost('location_id');
          $itemId = $this->getRequest()->getPost('item_id');
          $quoteId = $this->_checkoutSession->getQuote()->getId();
          $quote = $this->_quoteRepo->get($quoteId);

          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
          $connection = $resource->getConnection();
          $tableName = $resource->getTableName('locations');

          $storelocTable = $resource->getTableName('aw_storelocator_location');
          //Select Data from table
          $sql = "Select title,city,street,country_id,zip,phone FROM " . $storelocTable." WHERE location_id=".$locationId;
          $result = $connection->fetchAll($sql);

          $data['delivery_type'] = 1;
          $data['pickup_location_address'] = $result;
          $data['pickup_location'] = $locationId;

          $quoteTable = $resource->getTableName('quote_item');

          $sql = "UPDATE ". $quoteTable." SET `delivery_type` = '1', `pickup_location` = ".$locationId .", pickup_location_address = ". $result. " WHERE `item_id` =".$itemId;
          $result = $connection->query($sql);

          echo json_encode($result . $this->_checkoutSession->getQuote()->getId());

        //  echo json_encode($result);
      endif;
  }
}
?>
