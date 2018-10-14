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
         * Setting shipping step visibility to false
         *
         * @returns {mixin}
         */
        initialize: function () {
            this._super();

            this.visible(false);

            return this;
        }
    };

    /**
     * Mixin of Magento_Checkout/js/view/shipping
     */
    return function (target) {
        return target.extend(mixin);
    };
});
