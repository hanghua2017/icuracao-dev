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

use Dyode\Checkout\Api\Data\ShippingInformationInterface;
use Dyode\Checkout\Model\InfoProcessor\SaveManager;
use Magento\Checkout\Api\ShippingInformationManagementInterface;

class ShippingInfoProcessor
{

    /**
     * @var \Dyode\Checkout\Model\InfoProcessor\SaveManager
     */
    protected $manager;

    /**
     * @var \Magento\Checkout\Api\ShippingInformationManagementInterface
     */
    protected $shippingInformationManagement;

    /**
     * ShippingInfoProcessor constructor.
     *
     * @param \Magento\Checkout\Api\ShippingInformationManagementInterface $shippingInformationManagement
     * @param \Dyode\Checkout\Model\InfoProcessor\SaveManager $manager
     */
    public function __construct(
        ShippingInformationManagementInterface $shippingInformationManagement,
        SaveManager $manager
    ) {
        $this->shippingInformationManagement = $shippingInformationManagement;
        $this->manager = $manager;
    }
    
    /**
     * {@inheritDoc}
     */
    public function saveAddressInformation($cartId, ShippingInformationInterface $addressInformation)
    {
        $includeCuracaoCredit = true;
        $this->manager->saveQuoteItemShippingInfo($cartId, $addressInformation);
        $paymentDetails = $this->shippingInformationManagement->saveAddressInformation($cartId, $addressInformation);

        return $this->manager->updateShippingTotal($paymentDetails, $addressInformation, $includeCuracaoCredit);
    }
}
