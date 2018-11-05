<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       12/09/2018
 */

namespace Dyode\CheckoutDeliveryMethod\Controller\Deliverylocation;

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

      if ($this->getRequest()->getPost('location_id')):
          $locationId = $this->getRequest()->getPost('location_id');
          $itemId = $this->getRequest()->getPost('item_id');
          $quoteId = $this->_checkoutSession->getQuote()->getId();
          $quote = $this->_quoteRepo->get($quoteId);

          $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
          $resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
          $connection = $resource->getConnection();
          $selectedStore = '';

          $storelocTable = $resource->getTableName('aw_storelocator_location');
          //Select Data from table
          $sql = "Select image,title,city,street,country_id,zip,phone FROM " . $storelocTable." WHERE location_id=".$locationId;
          $result = $connection->fetchAll($sql);
          $storeManager = $objectManager->get('Magento\Store\Model\StoreManagerInterface');
          $media_url = $storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);

          $address ='';
          foreach ($result as $key=>$value) {
             $imageUrl = 'aheadworks/store_locator/'.$value['image'];
             $address .= $value['city'].", ".$value['street'].", ".$value['zip'];
             $selectedStore .= '<div class="left"><img src="'.$media_url.$imageUrl.'"></div>';
             $selectedStore .= '<div class="right"><div class="title">'.$value['title'].'</div>';
             $selectedStore .= '<div class="address">'.$value['city'].','.$value['street'].'</div>';
             $selectedStore .= '<div class="pincode">'.$value['zip'].'</div></div>';
          }

          $quoteTable = $resource->getTableName('quote_item');

          $sql = "UPDATE ". $quoteTable." SET `delivery_type` = '1', `pickup_location` = '".$locationId ."', pickup_location_address = '". $address. "' WHERE `item_id` =".$itemId;
          $result = $connection->query($sql);
          if ($result)
            echo json_encode($selectedStore);
          else
            echo 0;

        //  echo json_encode($result);
      endif;
  }
}
