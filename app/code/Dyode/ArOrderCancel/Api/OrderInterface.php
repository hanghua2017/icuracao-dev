<?php

/**
 * Copyright 2018 Magento. All rights reserved.
 */

namespace Dyode\ArOrderCancel\Api;

/**
 * Interface OrderCancel
 * @category Dyode
 * @package  Dyode_ArOrderCancel
 * @author   Nithin
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
     * @param bool $refundShipping       
     * @param string $comment   
     * @param bool $wholeOrder 
     * @return bool
     */
    public function cancelOrder($orderId, $sku, $quantity, $refundShipping = false, $comment, $wholeOrder );
}