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
         * Make summary totals always visible
         *
         * @return {*}
         */
        isDisplayed: function () {
            return true;
        }
    };

    /**
     * Mixin of Magento_Checkout/js/view/summary/totals
     */
    return function (target) {
        return target.extend(mixin);
    };
});
