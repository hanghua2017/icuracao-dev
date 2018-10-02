<?php
/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_ShippingOrder
 * @author    Sooraj Sathyan (soorajcs.mec@gmail.com)
 */
namespace Dyode\ShippingOrder\Model\Order;

class Invoice extends \Magento\Framework\Model\AbstractModel// implements \Magento\Framework\DataObject\IdentityInterface
{
    /**
     * @var \Magento\Sales\Api\OrderRepositoryInterface
     */
    protected $_orderRepository;

    /**
     * @var \Magento\Sales\Model\Service\InvoiceService
     */
    protected $_invoiceService;

    /**
     * @var \Magento\Framework\DB\Transaction
     */
    protected $_transaction;

     /**
     * Construct
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
     * @param \Magento\Sales\Model\Service\InvoiceService $invoiceService
     * @param \Magento\Framework\DB\Transaction $transaction
     * @param \Magento\Framework\Registry $data
     */
	public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Sales\Model\Service\InvoiceService $invoiceService,
        \Magento\Framework\DB\Transaction $transaction,
        \Magento\Framework\Registry $data
    ) {
        $this->_orderRepository = $orderRepository;
        $this->_invoiceService = $invoiceService;
        $this->_transaction = $transaction;
		return parent::__construct($context, $data);
    }

    public function createInvoice($orderId)
    {
        $order = $this->_orderRepository->get($orderId);
        if ($order->canInvoice()) {
            # code...
            $invoice = $this->_invoiceService->prepareInvoice($order);
            $invoice->register();
            try {
                $invoice->save();
                $transactionSave = $this->_transaction->addObject($invoice)->addObject($invoice->getOrder());
                $transactionSave->save();
                // $this->invoiceSender->send($invoice);   # Invoice Sending
                //send notification code
                $order->addStatusHistoryComment(
                    __('Notified customer about invoice #%1.', $invoice->getId())
                    )
                    ->setIsCustomerNotified(true)
                    ->save();
            } catch (\Exception $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                __($e->getMessage())
                );
            }
        }
        return;
    }
}