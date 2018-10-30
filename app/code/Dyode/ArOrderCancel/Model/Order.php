<?php

/**
 * Copyright 2018 Magento. All rights reserved.
 */

namespace Dyode\ArOrderCancel\Model;

use Dyode\ArOrderCancel\Api\OrderInterface;

/**
 * Defines the implementaiton class of the order cancellation.
 * Class Order
 * @category Dyode
 * @package  Dyode_ArOrderCancel
 * @author   Nithin
 */
class Order implements OrderInterface
{
    /**
    * constructor function
    */
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
     * @param bool $refundShipping
     * @param string $comment
     * @param bool $wholeOrder
     * @return bool
     */
    public function cancelOrder($orderId, $sku, $quantity, $refundShipping = false, $comment, $wholeOrder )
    {

     try {
            //load order details
            $order = $this->order->loadByIncrementId($orderId);

            if(!$order->getId()){
                throw new \Exception('Order not found'); //order not found
            }
            $orderStatus = $order->getStatus();

            //checks whether the order is already closed
            if($orderStatus == 'closed'){
                throw new \Exception('Order is already closed');
            }

            //checks whether the order is already canceled
            if($orderStatus == 'canceled'){
                throw new \Exception('Order is already canceled');
            }

            //unholds an order
            if($order->canUnhold()) {
                $order->unhold()->save();
            }

            if($wholeOrder){
                if($order->canCancel()){
                    $order->cancel();
                    //add order history
                    $history = $order->addStatusHistoryComment($comment);
                    $history->setIsCustomerNotified(true); // for backwards compatibility
                    $order->save();
                } else{
                    //get all invoices
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
                //get a specific order item
                $item = $this->getItemId($order, $sku);
                if($item){
                    if($order->canCancel()){
                        $orderItems = $order->getAllItems();
                        foreach ($orderItems as $value) {
                           if($value['sku']==$sku){
                                //quantity validation
                                if($value['qty_ordered']>=$quantity){
                                    $value->setQtyCanceled($quantity);
                                    $value->save();
                                } else{
                                    throw new \Exception('Item quantity exceeded');
                                }
                            }
                        }
                        //add order history
                        $history = $order->addStatusHistoryComment($comment);
                        $history->setIsCustomerNotified(true); // for backwards compatibility
                        $order->save();
                    }else{
                        //cancel invoiced items
                        $this->cancelInvoicedItem($order, $sku, $quantity, $refundShipping, $comment);
                        //add order history
                        $history = $order->addStatusHistoryComment($comment);
                        $history->setIsCustomerNotified(true); // for backwards compatibility
                        $order->save();
                    }
                }else{
                    throw new \Exception('Order item not found');
                }
            }
        } catch (\Exception $ex) {}

        if(empty($ex)){
            $returnData['INFO'] = 'Order items have been cancelled';
            $returnData['OK'] = true;
        } else {
            $returnData['ERROR'] = $ex->getMessage();
            $returnData['OK'] = false;
        }
        //return api response
        return json_encode($returnData);
    }

    /**
     * Cancel invoiced item
     *
     * @param string $order
     * @param string $sku
     * @param string $qty
     * @param string $refundShipping
     * @param string $comment
     */
    public function cancelInvoicedItem($order, $sku, $qty, $refundShipping, $comment){
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
        //add order history
        $history = $order->addStatusHistoryComment($comment);
        $history->setIsCustomerNotified(true); // for backwards compatibility
        $order->save();
    }

    /**
     * get Id of the order item
     *
     * @param string $order
     * @param string $sku
     */
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
