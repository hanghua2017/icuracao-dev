<?php
namespace Dyode\Signifyd\Model;

use \Magento\Framework\Model\AbstractModel;

/**
 * Class  Signifyd
 * @category Dyode
 * @package  Dyode_Signifyd
 * @author   Nithin
 */
class Signifyd extends \Magento\Framework\View\Element\Template
{
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Sales\Api\Data\OrderInterface $orderInterface,
        \Magento\Signifyd\Api\CaseManagementInterface $caseManagement,
        array $data = []
    ) {
        $this->orderInterface = $orderInterface;
        $this->caseManagement = $caseManagement;
        parent::__construct($context, $data);
    }

    /**
     * function name : processSignifyd
     * description : for getting signifyd status and processing orders
     * @param int $orderid
     * @return no return
     */
    public function processSignifyd($orderid)
    {
        //load order from incremental Id
        $order = $this->orderInterface->loadByIncrementId($orderid);
        $orderIncrementId = $order->getEntityId();
        //get Signifyd Status
        $guranteeStatus = $this->caseManagement->getByOrderId($orderIncrementId)->getGuaranteeDisposition();
        if ($guranteeStatus == 'CANCELED') {
            $order->setStatus('pending_cancellation');
            $historyitem = $order->addStatusHistoryComment('Signifyd Status-'.$guranteeStatus);
            //Notify customer
            $historyitem->setIsCustomerNotified(true);
            $order->save();
        } elseif ($guranteeStatus == 'DECLINED') {
            $order->setStatus('fraud');
            $historyitem = $order->addStatusHistoryComment('Signifyd Status-'.$guranteeStatus);
            //Notify customer
            $historyitem->setIsCustomerNotified(true);
            $order->save();
        } else {
            $historyitem = $order->addStatusHistoryComment('Signifyd Status-'.$guranteeStatus);
            $order->save();
        }
    }
}
