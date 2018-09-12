<?php

/**
 * Copyright 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dyode\ArOrderCancel\Api;

/**
 * Defines the service contract for some simple maths functions. The purpose is
 * to demonstrate the definition of a simple web service, not that these
 * functions are really useful in practice. The function prototypes were therefore
 * selected to demonstrate different parameter and return values, not as a good
 * calculator design.
 */
interface OrderInterface
{
    /**
     * Cancel order and add order comment
     *
     * @api
     * @param string $orderId 
     * @param string $sku 
     * @param int $quantity 
     * @param string $comment
     * @param bool $refundShipping     
     * @param bool $wholeOrder 
     * @return bool
     */
    public function cancelOrder($orderId, $sku, $quantity, $refundShipping = false, $comment, $wholeOrder );
}