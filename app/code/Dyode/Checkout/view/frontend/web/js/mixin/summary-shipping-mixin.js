/**
 * Dyode_Checkout Magento2 Module.
 *
 * Extending Magento_Checkout core module.
 *
 * @module    Dyode_Checkout
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define(function () {

    var mixin = {

        /**
         * We dont want to show shipping method title as there is no shipping against quote.
         * We can have multiple shipping methods since it is keeping in the quote item level.
         * @returns {String}
         */
        getShippingMethodTitle: function () {
            return '';
        },

        /**
         * Avoid quote.shippingMethod exists check since it wont apply in our case.
         * @returns {Boolean}
         */
        isCalculated: function () {
            return this.totals() && this.isFullMode();
        }
    };

    /**
     * Mixin of Magento_Tax/js/view/checkout/summary/shipping
     */
    return function (target) {
        return target.extend(mixin);
    };
});
