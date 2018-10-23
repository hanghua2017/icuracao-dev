<?php
/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout core module.
 *
 * @pakcage   Dyode
 * @module    Dyode_Checkout
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */
namespace Dyode\Checkout\Model;

use Magento\Quote\Model\QuoteIdMaskFactory;
use Magento\Checkout\Api\ShippingInformationManagementInterface;
use Dyode\Checkout\Api\GuestShippingInfoInterface;
use Dyode\Checkout\Api\Data\ShippingInformationInterface;
use Dyode\Checkout\Model\InfoProcessor\SaveManager;

/**
 * Guest ShippingInfo Processor
 *
 * This will update the shipping carrier information against each quote item.
 * It also performs Magento's default shipping info saving action.
 */
class GuestShippingInfoProcessor implements GuestShippingInfoInterface
{
    /**
     * @var \Dyode\Checkout\Model\InfoProcessor\SaveManager
     */
    protected $manager;

    /**
     * @var \Magento\Quote\Model\QuoteIdMask
     */
    protected $quoteMask;

    /**
     * @var \Magento\Checkout\Api\ShippingInformationManagementInterface
     */
    protected $shippingInformationManagement;

    /**
     * GuestShippingInfoProcessor constructor.
     *
     * @param \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement
     * @param \Magento\Quote\Model\QuoteIdMaskFactory $quoteIdMaskFactory
     * @param \Dyode\Checkout\Model\InfoProcessor\SaveManager $manager
     */
    public function __construct(
        ShippingInformationManagementInterface $shippingInformationManagement,
        QuoteIdMaskFactory $quoteIdMaskFactory,
        SaveManager $manager
    ) {
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->manager = $manager;
    }

    /**
     * {@inheritDoc}
     */
    public function saveAddressInformation($cartId, ShippingInformationInterface $addressInformation)
    {
        /** @var $quoteIdMask \Magento\Quote\Model\QuoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');
        $this->manager->saveQuoteItemShippingInfo($quoteIdMask->getQuoteId(), $addressInformation);
        $paymentDetails = $this->shippingInformationManagement->saveAddressInformation(
            $quoteIdMask->getQuoteId(),
            $addressInformation
        );

        return $this->manager->updateShippingTotal($paymentDetails, $addressInformation);
    }
}
