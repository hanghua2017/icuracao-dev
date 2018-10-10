<?php
/**
 * Dyode_CheckoutAddressStep Magento2 Module.
 *
 * Adding new checkout step in the one page checkout.
 *
 * @package   Dyode
 * @module    Dyode_CheckoutAddressStep
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */
namespace Dyode\CheckoutAddressStep\Model\GuestCart;

use Dyode\CheckoutAddressStep\Api\Data\AddressInterface;
use Dyode\CheckoutAddressStep\Api\GuestShipmentEstimationInterface;
use Dyode\CheckoutAddressStep\Api\ShipmentEstimationInterface;
use Magento\Quote\Api\ShippingMethodManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

class GuestShippingMethodManagement implements GuestShipmentEstimationInterface
{
    /**
     * @var ShippingMethodManagementInterface
     */
    private $shippingMethodManagement;

    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var ShipmentEstimationInterface
     */
    private $shipmentEstimationManagement;

    /**
     * GuestShippingMethodManagement constructor.
     *
     * @param \Magento\Quote\Api\ShippingMethodManagementInterface $shippingMethodManagement
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Dyode\CheckoutAddressStep\Api\ShipmentEstimationInterface $shipmentEstimation
     */
    public function __construct(
        ShippingMethodManagementInterface $shippingMethodManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        ShipmentEstimationInterface $shipmentEstimation
    ) {
        $this->shippingMethodManagement = $shippingMethodManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->shipmentEstimationManagement = $shipmentEstimation;
    }

    /**
     * @inheritdoc
     */
    public function estimateByExtendedAddress($cartId, AddressInterface $address)
    {
        /** @var $quoteIdMask QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $this->shipmentEstimationManagement->estimateByExtendedAddress(
            (int)$quoteIdMask->getQuoteId(),
            $address
        );
    }
}