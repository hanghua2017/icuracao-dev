<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       06/09/2018
 */

namespace Dyode\DeliveryMethod\Api;

/**
 * Store selection app management
 * @api
 * @since 100.0.2
 */
interface StorePickupInterface
{
    /**
     * Select Store pickup
     *
     * @param int $cartId
     * @return bool
     */
    public function selection($zipcode);

}
