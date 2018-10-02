<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ShippingOrder
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
 */
namespace Dyode\ShippingOrder\Model\Order;

class Shipment extends \Magento\Framework\Model\AbstractModel// implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */  
    protected $_orderRepository;
 
    /**
     * @var \Magento\Sales\Model\Convert\Order
     */
    protected $_convertOrder;
 
    /**
     * @var \Magento\Shipping\Model\ShipmentNotifier
     */
    protected $_shipmentNotifier;
 
    /**
     * Construct
     * 
     * @param \Magento\Framework\Model\Context                                     $context
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Convert\Order $convertOrder
     * @param \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier
     * @param \Magento\Framework\Registry $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Convert\Order $convertOrder,
        \Magento\Shipping\Model\ShipmentNotifier $shipmentNotifier,
        \Magento\Framework\Registry $data
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_convertOrder = $convertOrder;
        $this->_shipmentNotifier = $shipmentNotifier;
        return parent::__construct($context, $data);
    }

    public function createShipment($orderId)
    {
        $order = $this->_orderRepository->get($orderId);
 
        // to check order can ship or not
        if (!$order->canShip()) {
            throw new \Magento\Framework\Exception\LocalizedException(
            __('You cant create the Shipment of this order.') );
        }
        
        $orderShipment = $this->_convertOrder->toShipment($order);
        
        foreach ($order->getAllItems() AS $orderItem) {
 
         // Check virtual item and item Quantity
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }
            
            $qty = $orderItem->getQtyToShip();
            $shipmentItem = $this->_convertOrder->itemToShipmentItem($orderItem)->setQty($qty);
    
            $orderShipment->addItem($shipmentItem);
        }
        $orderShipment->register();
        $orderShipment->getOrder()->setIsInProcess(true);
        try {
 
            // Save created Order Shipment
            $orderShipment->save();
            $orderShipment->getOrder()->save();
 
            // Send Shipment Email
            $this->_shipmentNotifier->notify($orderShipment);
            $orderShipment->save();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(
            __($e->getMessage())
            );
        }
    }
}