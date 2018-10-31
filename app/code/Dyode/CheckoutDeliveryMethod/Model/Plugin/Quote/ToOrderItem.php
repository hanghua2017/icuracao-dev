<?php
    namespace Dyode\CheckoutDeliveryMethod\Model\Plugin\Quote;

    class ToOrderItem {

        /**
         *
         * @var type \Magento\Catalog\Model\Product
         */
        protected $productRepository;

        /**
         * @param \Magento\Catalog\Model\Product $productRepository
         */
        public function __construct(
        \Magento\Catalog\Model\Product $productRepository
        ) {
            $this->productRepository = $productRepository;
        }

        /**
         *
         * @param \Magento\Quote\Model\Quote\Item\ToOrderItem $subject
         * @param \Vendorname\Modulename\Plugin\Model\Quote\Item\callable $proceed
         * @param \Magento\Quote\Model\Quote\Item\AbstractItem $item
         * @param type $additional
         * @return type
         */
        public function aroundConvert(
            \Magento\Quote\Model\Quote\Item\ToOrderItem $subject, 
            callable $proceed,
            \Magento\Quote\Model\Quote\Item\AbstractItem $item,
            $additional = []
        ) {

            $writer = new \Zend\Log\Writer\Stream(BP . "/var/log/QuoteToOrder.log");
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
          
            $orderItem = $proceed($item, $additional);
            $logger->info("pickupLocation : " .  $item->getDeliveryType());

            $orderItem->setDeliveryType($item->getDeliveryType());
            $orderItem->setPickupLocation($item->getPickupLocation());
            $orderItem->setPickupLocationAddress($item->getPickupLocationAddress());

            if ( $item->getWarrantyParentItemId() ) {
                $orderItem->setWarrantyParentItemId( $item->getWarrantyParentItemId() );
            }
            if ( $item->getShippingDetails() ) {
                $orderItem->setShippingDetails( $item->getShippingDetails() );
                $orderItem->setShippingCost( $item->getShippingCost() );
            }

            return $orderItem;
        }
    }

