<?php
/**
 * Dyode_CheckoutDeliveryMethod Magento2 Module.
 *
 * Add a new checkout step in checkout
 *
 * @package   Dyode
 * @module    Dyode_CheckoutDeliveryMethod
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\CheckoutDeliveryMethod\Controller\Storelocator;


use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\NoSuchEntityException;

class Setstores extends Action
{
  
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Psr\Log\LoggerInterface $logger
     */
    protected $logger;

    protected $_quoteRepo;

    /**
     * Setstores constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * 
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,

        Context $context,

        \Magento\Quote\Model\QuoteRepository $quoteRepo,
        
        JsonFactory $resultJsonFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->logger = $logger;
        $this->_quoteRepo = $quoteRepo;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $quoteId = (int)$this->getRequest()->getParam('quoteId');
        $quoteItemsData = $this->getRequest()->getParam('quoteItemsData');
        # Get quote item data from request 
        $quoteItemsReq = json_decode($quoteItemsData);
        # Load quote from quote id
        $quote = $this->_quoteRepo->get($quoteId);
        if (!$quote) {
            $this->logger->info("CRITICAL!!!!!! Quote not loaded");
        }
        $this->logger->info('Quote item loaded. Now iterate and set data');
        if (!empty($quoteItemsReq)) {
            foreach ($quoteItemsReq as $quoteItemReq) {
                $this->logger->info("quoteItemData".print_r($quoteItemReq, true));
                $quoteItemId = $quoteItemReq->quoteItemId;
                $delivery_type = $quoteItemReq->deliveryType;
                $this->logger->info("Quote Item Id: $quoteItemId Delivery Type: $delivery_type");
                
                
                # Load quote item by quoteitem id
                $quoteItem = $quote->getItemById($quoteItemId);
                    
                # Set delivery type  
                $quoteItem->setDeliveryType($delivery_type);

                # TODO set store location It is present in migrated db so no need to create script
                # $quoteItem->setStoreLocation($store_location);
                
                $quoteItem->save();
            }
            $quote->collectTotals()->save();
            $this->logger->info("Successfull");
        } else {
            # Incase we failed to access the quote item details from request object 
            $this->logger->info("Quote items not sent from request");    
        }

        
        
    }

    

}
