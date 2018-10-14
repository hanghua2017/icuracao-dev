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
         * @return {Boolean}
         */
        isExcludingDisplayed: function () {
            return false; //eslint-disable-line eqeqeq
        }
    };

    return function (target) {
        return target.extend(mixin);
    };
});
