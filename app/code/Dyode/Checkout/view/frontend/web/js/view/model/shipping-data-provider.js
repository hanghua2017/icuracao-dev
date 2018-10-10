/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout shipping core js file
 *
 * @module    Dyode_Checkout
 * @author    Mathew Joseph <mathew.joseph@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

/**
  * This holds shipping method information over each quote items in a quote.
  */
define(['ko'], function (ko) {

    return {
        shippingInfo: ko.observableArray([])
    };
});
