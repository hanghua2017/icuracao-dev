<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       13/08/2018
 */

namespace Dyode\Checkout\Api;

/**
 * Credit app management
 * @api
 * @since 100.0.2
 */
interface CreditManagementInterface
{
    /**
     * Apply Curacao credit
     *
     * @param int $cartId
     * @return bool
     */
    public function apply($cartId);
    /**
    * Remove store credit
    *
    * @param int $cartId
    * @return bool
    */
    public function removecredit($cartId);
}
