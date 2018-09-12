<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dyode\ArOrderCancel\Model;

use Dyode\ArOrderCancel\Api\OrderInterface;

/**
 * Defines the implementaiton class of the calculator service contract.
 */
class Order implements OrderInterface
{
    public function __construct(
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Api\Data\OrderInterface $order,
        \Magento\Sales\Model\Order\CreditmemoFactory $creditmemoFactory,
        \Magento\Sales\Model\Order\Invoice $invoice,
        \Magento\Sales\Model\Service\CreditmemoService $creditmemoService,
        \Magento\Sales\Model\RefundOrder $refundOrder,
    \Magento\Sales\Model\Order\Creditmemo\ItemCreationFactory $itemCreationFactory,
    \Magento\Sales\Model\Order\CreditmemoDocumentFactory $creditmemoDocumentFactory
    ) {
        $this->orderRepository = $orderRepository;
        $this->order = $order;
        $this->creditmemoFactory = $creditmemoFactory;
        $this->creditmemoService = $creditmemoService;
        $this->invoice = $invoice;
        $this->refundOrder = $refundOrder;
        $this->itemCreationFactory = $itemCreationFactory;
        $this->creditmemoDocumentFactory = $creditmemoDocumentFactory;
    }

    /**
     * Cancel order and add order comment
     *
     * @api
     * @param string $orderId 
     * @param string $sku 
     * @param int $quantity 
     * @param string $comment
     * @param bool $refundShipping
     * @param bool $wholeOrder 
     * @return bool
     */
    public function cancelOrder($orderId, $sku, $quantity, $refundShipping = false, $comment, $wholeOrder ) {

     $order = $this->order->loadByIncrementId($orderId);  
     
     try {
            $order = $this->order->loadByIncrementId($orderId);

            if(!$order->getId()){
                throw new \Exception('Order not found');
            }
            $orderStatus = $order->getStatus();

            if($orderStatus == 'closed'){
                throw new \Exception('Order is already closed');
            }

            if($orderStatus == 'canceled'){
                throw new \Exception('Order is already canceled');
            }

            if($wholeOrder){
                if($order->canCancel()){
                    $order->cancel();
                    $history = $order->addStatusHistoryComment($comment);
                    $history->setIsCustomerNotified(true); // for backwards compatibility
                    $order->save();
                } else{
                    $invoices = $order->getInvoiceCollection();
                    foreach ($invoices as $invoice) {
                        $invoiceincrementid = $invoice->getIncrementId();
                    }
                    $invoiceobj = $this->invoice->loadByIncrementId($invoiceincrementid);
                    $creditmemo = $this->creditmemoFactory->createByOrder($order);
                    // Don't set invoice if you want to do offline refund
                    $creditmemo->setInvoice($invoiceobj);
                    $this->creditmemoService->refund($creditmemo); 
                    $history = $order->addStatusHistoryComment($comment);
                    $history->setIsCustomerNotified(true); // for backwards compatibility
                    $order->save();
                }
            } else {
                $item = $this->getItemId($order, $sku);
                if($item){
                    if($order->canCancel()){
                        $orderItems = $order->getAllItems();        
                        foreach ($orderItems as $value) {
                           if($value['sku']==$sku){
                                if($value['qty_ordered']>=$quantity){
                                    $value->setQtyCanceled($quantity);
                                    $value->save(); 
                                } else{
                                    throw new \Exception('Item quantity exceeded');
                                }   
                            }
                        }
                        $history = $order->addStatusHistoryComment($comment);
                        $history->setIsCustomerNotified(true); // for backwards compatibility
                        $order->save();
                    }else{
                        $this->cancelInvoicedItem($order, $sku, $quantity, $refundShipping);
                        $history = $order->addStatusHistoryComment($comment);
                        $history->setIsCustomerNotified(true); // for backwards compatibility
                        $order->save();
                    } 
                }else{
                    throw new \Exception('Order item not found');
                }   
            }
            return true;
        
        } catch (\Exception $ex) {
            throw $ex;
        }
    }


    public function cancelInvoicedItem($order, $sku, $qty, $refundShipping){
        $invoices = $order->getInvoiceCollection();
        foreach ($invoices as $invoice) {
            $invoiceincrementid = $invoice->getIncrementId();
        }
        $itemId = $this->getItemId($order, $sku);
        $data = array(
            'qtys' => array(
            $itemId => $qty
          )
        );
        $invoiceobj = $this->invoice->loadByIncrementId($invoiceincrementid);
        $creditmemo = $this->creditmemoFactory->createByOrder($order,$data);
        if(!$refundShipping){
            $shippingAmount = $creditmemo->getShippingAmount();
            $baseShippingAmount = $creditmemo->getBaseShippingAmount();
            $baseShippingTaxAmount = $creditmemo->getBaseShippingTaxAmount();
            $shippingTaxAmount = $creditmemo->getShippingTaxAmount();
            $creditmemo->setBaseShippingAmount('0');
            $creditmemo->setShippingAmount('0');
            $creditmemo->setBaseShippingTaxAmount('0');
            $creditmemo->setShippingTaxAmount('0');
            $creditmemo->setGrandTotal($creditmemo->getGrandTotal() - $shippingAmount - $shippingTaxAmount);
            $creditmemo->setBaseGrandTotal($creditmemo->getBaseGrandTotal() - $baseShippingTaxAmount - $baseShippingAmount); 
        }
        
        // Don't set invoice if you want to do offline refund
        $creditmemo->setInvoice($invoiceobj);
        $this->creditmemoService->refund($creditmemo); 
        $history = $order->addStatusHistoryComment($comment);
        $history->setIsCustomerNotified(true); // for backwards compatibility
        $order->save();
    }

    public function getItemId($order, $sku){
        $orderItems = $order->getAllItems();        
        foreach ($orderItems as $value){
            if($value['sku'] == $sku){
                return $value->getId();
            }                       
        }
        return false;
    }   
}