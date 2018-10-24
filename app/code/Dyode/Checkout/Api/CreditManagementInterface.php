<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       13/08/2018
 */
namespace Dyode\Checkout\Api;

/**
 * Credit app management
 *
 * @api
 */
interface CreditManagementInterface
{
    /**
     * Apply Curacao credit
     *
     * @param string $cartId
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function applyCuracaoCreditInTotals($cartId);

    /**
     * Remove Curacao credit
     *
     * @param string $cartId
     * @return \Magento\Checkout\Api\Data\PaymentDetailsInterface
     */
    public function removeCuracaoCreditFromTotals($cartId);
}
