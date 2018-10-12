<?php

namespace Dyode\CheckoutAddressStep;

use Dyode\CheckoutAddressStep\Api\ShippingInformationManagementInterface;
use Magento\Quote\Api\CartApiRepositoryInterface;
use Magento\Quote\Api\Data\CartExtensionFactory;
use Magento\Quote\Model\ShippingAssignmentFactory;
use Magento\Quote\Model\Quote\Item;
use Magento\Checkout\Api\Data\PaymentDetailsInterface;

class ShippingInformationManagement extends ShippingInformationManagementInterface{

    /**
    * @var \Magento\Quote\Api\CartRepositoryInterface
    */
    protected $quoteRepository;

    /**
    * @var \Magento\Quote\Api\Data\CartExtensionFactory
    */
    private $cartExtensionFactory;

    /**
    * @var \Magento\Quote\Model\ShippingAssignmentFactory
    */
    protected $shippingAssignmentFactory;

    /*
    * @var Magento\Quote\Model\Quote\Item
    */
    protected $quoteItem;

    /**
     * Constructor
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Magento\Quote\Api\Data\CartExtensionFactory $cartExtensionFactory
     * @param \Magento\Quote\Model\ShippingAssignmentFactory $shippingAssignmentFactory
     */
    public function __construct(
        CartApiRepositoryInterface $quoteRepository,
        CartExtensionFactory $cartExtensionFactory,
        ShippingAssignmentFactory $shippingAssignmentFactory,
        Item $quoteItem   
    ){
        $this->quoteRepository = $quoteRepository;
        $this->cartExtensionFactory = $cartExtensionFactory;
        $this->shippingAssignmentFactory = $shippingAssignmentFactory;
        $this->quoteItem = $quoteItem;
    }

    /**
     * {@inheritDoc}
     */
    public function saveAddressInformation(
        $cartId,
        \Dyode\CheckoutAddressStep\Api\Data\ShippingInformationInterface $addressInformation
    ) {

        $address = $addressInformation->getShippingAddress();
        $billingAddress = $addressInformation->getBillingAddress();

        //Carrier code will be set as null
        $carrierCode = $addressInformation->getShippingCarrierCode();
        $methodCode = $addressInformation->getShippingMethodCode();

        //Carrier method array for each quote item
        $shippingCarrierInfo = $addressInformation->getShippingCarrierInfo();

        if (!$address->getCustomerAddressId()) {
            $address->setCustomerAddressId(null);
        }

        if (!$address->getCountryId()) {
            throw new StateException(__('Shipping address is not set'));
        }

        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->getActive($cartId);
        $quote = $this->prepareShippingAssignment($quote, $address, '');

        // update the quote item with the shipping Information
        $quote = $this->updateShippingInfo($quote,$shippingCarrierInfo);
    
        $this->validateQuote($quote);
        $quote->setIsMultiShipping(false);

        if ($billingAddress) {
            $quote->setBillingAddress($billingAddress);
        }

        try {
            $this->quoteRepository->save($quote);
        } catch (\Exception $e) {
            $this->logger->critical($e);
            throw new InputException(__('Unable to save shipping information. Please check input data.'));
        }

        

        /** @var \Magento\Checkout\Api\Data\PaymentDetailsInterface $paymentDetails */
        $paymentDetails = $this->paymentDetailsFactory->create();
        $paymentDetails->setPaymentMethods($this->paymentMethodManagement->getList($cartId));
        $paymentDetails->setTotals($this->cartTotalsRepository->get($cartId));
        return $paymentDetails;


    }

    /**
     * @param CartInterface $quote
     * @param AddressInterface $address
     * @param string $shippingCarrierInfo
     * @return CartInterface
     */
    private function prepareShippingAssignment($quote, $address, $method){

        $cartExtension = $quote->getExtensionAttributes();
        if ($cartExtension === null) {
            $cartExtension = $this->cartExtensionFactory->create();
        }

        $shippingAssignments = $cartExtension->getShippingAssignments();
        if (empty($shippingAssignments)) {
            $shippingAssignment = $this->shippingAssignmentFactory->create();
        } else {
            $shippingAssignment = $shippingAssignments[0];
        }

        $shipping = $shippingAssignment->getShipping();
        if ($shipping === null) {
            $shipping = $this->shippingFactory->create();
        }

        
        $shipping->setAddress($address);
        
        $shipping->setMethod($method);
        $shippingAssignment->setShipping($shipping);
        $cartExtension->setShippingAssignments([$shippingAssignment]);
        return $quote->setExtensionAttributes($cartExtension);
    }

    /**
     * @param CartInterface $quote
     * @param string $shippingCarrierInfo
     * @return CartInterface
     */

    private function updateShippingInfo($quote, $shippingCarrierInfo){
        //decode the shipping Info
        $shippingInfo = json_decode($shippingCarrierInfo);
        
        foreach($shippingInfo as $quoteItemId => $carrierInfo){
            $this->quoteItem = $quote->getItemById($quoteItemId);

           //If no quoteItem found continue the loop
           if (!$quoteItem) {
               continue;
           }
           $this->quoteItem->setShippingDetails(json_encode($carrierInfo));
        }
        $quote->save();
    }

      /**
     * Validate quote
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @throws InputException
     * @throws NoSuchEntityException
     * @return void
     */
    protected function validateQuote(\Magento\Quote\Model\Quote $quote)
    {
        if (0 == $quote->getItemsCount()) {
            throw new InputException(__('Shipping method is not applicable for empty cart'));
        }
    }
}
?>