<?php
namespace Dyode\CheckoutDeliveryMethod\Controller\DeliveryMethods;

use Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Save extends Action
{

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;

    /**
     * @var \Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod
     */
    protected $deliveryMethod;

    /**
     * Save constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod $deliveryMethod
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        Context $context,
        DeliveryMethod $deliveryMethod,
        JsonFactory $resultJsonFactory
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->deliveryMethod = $deliveryMethod;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $quoteId = $this->getRequest()->getParam('quoteId', false);
        $quoteItemsData = $this->getRequest()->getParam('quoteItemsData', []);
        $quoteItemsReq = (array)json_decode($quoteItemsData, true);

        if ($quoteId && count($quoteItemsReq) > 0) {
            try {
                $this->deliveryMethod->save($quoteId, $quoteItemsReq);
                return $this->resultJsonFactory->create([
                    'success' => __('delivery information updated successfully.')
                ]);
            } catch (\Exception $exception) {
                return $this->resultJsonFactory->create(['error' => __($exception->getMessage())]);
            }
        } else {
            return $this->resultJsonFactory->create([
                'error' => __('Given data is insufficient to update the delivery information.')
            ]);
        }
    }

}
