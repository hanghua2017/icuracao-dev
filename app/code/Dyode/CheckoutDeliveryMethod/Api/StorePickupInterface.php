<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       06/09/2018
 */

namespace Dyode\CheckoutDeliveryMethod\Api;

/**
 * Store selection app management
 *
 * @api
 * @since 100.0.2
 */
interface StorePickupInterface
{
    /**
     * Select Store pickup
     *
     * @param int $zipcode
     * @return bool
     */
    public function selection($zipcode);

}
