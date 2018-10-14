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
         * This will hide shipping method information from summary section.
         *
         * @return {Boolean}
         */
        isExcludingDisplayed: function () {
            return false; //eslint-disable-line eqeqeq
        }
    };

    /**
     * Mixin of Magento_Tax/js/view/checkout/summary/shipping
     */
    return function (target) {
        return target.extend(mixin);
    };
});
