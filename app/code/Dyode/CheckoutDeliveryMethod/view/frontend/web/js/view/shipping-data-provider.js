/**
 * Dyode_CheckoutDeliveryMethod Magento2 Module.
 *
 * Add a new checkout step in checkout
 *
 * @module    Dyode_CheckoutDeliveryMethod
 * @copyright Copyright © Dyode
 * @author Rajeev K Tomy <rajeev.ktomy@dyode.com>
 */

/**
  * This holds shipping method information over each quote items in a quote.
  */
define(['ko'], function (ko) {
    'use strict';

    return {
        shippingInfo: ko.observableArray([])
    }
});
