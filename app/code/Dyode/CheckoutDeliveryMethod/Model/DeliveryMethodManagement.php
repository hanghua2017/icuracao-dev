<?php
namespace Dyode\CheckoutDeliveryMethod\Model;
 
use Magento\Quote\Api\CartRepositoryInterface;
use Dyode\CheckoutDeliveryMethod\Api\DeliveryMethodManagementInterface as ApiInterface;
 
class DeliveryMethodManagement implements ApiInterface {
 
    /**
     * Quote / Cart Repository
     * @var CartRepositoryInterface $quoteRepository
     */
    protected $quoteRepository;

    /**
     * Quote Item Factory
     * @var CartRepositoryInterface $quoteRepository
     */
    private $quoteItemFactory;
    /**
     * Item Resource Model
     * @var CartRepositoryInterface $quoteRepository
     */
    private $itemResourceModel;
 
    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory
     * @param \Magento\Quote\Model\ResourceModel\Quote\Item $itemResourceModel
     */
    public function __construct
    (
        CartRepositoryInterface $quoteRepository,
        \Magento\Quote\Model\Quote\ItemFactory $quoteItemFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Item $itemResourceModel
    ) 
    {
        $this->quoteRepository = $quoteRepository;
        $this->quoteItemFactory = $quoteItemFactory;
        $this->itemResourceModel = $itemResourceModel;
    }
 
    /**
     * Updates the specified quote items delivery_type and store_location attributes.
     *
     * @api
     * @param int $cartId
     * @param \Dyode\DeliveryMethod\Api\Data\DeliveryMethodInformation[] $quoteItemsPostData
     * @return string $response
     */
    public function updateDeliveryMethodOnQuote($cartId, $quoteItemsPostData = null) {
        
        try {
            // Get active quote by id
            $quote = $this->quoteRepository->getActive($cartId);

            /** 
             * Iterate through the quote items and set delivery type for specific products
             * with corresponding quote item id
             * */ 
            if (!empty($quoteItemsPostData)) {
                foreach ($quoteItemsPostData as $quoteItemData) {
                    
                    # Get quote item id 
                    $quoteItemId = $quoteItemData->getQuoteId();
                    # Get quote item delivery type
                    $deliveryType = $quoteItemData->getDeliveryType();

                    # Load quote item by id
                    $quoteItem = $quote->getItemById($quoteItemId);
                    
                    # Set delivery type  
                    $quoteItem->setDeliveryType($delivery_type);

                    # TODO set store location
                    # $quoteItem->setStoreLocation($store_location);
                    
                    
                }
            }
            
            
            # Reload quote 
            $this->quoteRepository->save($quote->collectTotals());
        } catch (\Exception $e) {
            $this->createLog($e);
            $returnArray['error'] = __('Unable to process request');
            $returnArray['status'] = 500;
            return $returnArray;
        }
 
        return true;
    }
}