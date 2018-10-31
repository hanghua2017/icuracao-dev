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

define(['mage/translate'], function ($t) {

    var mixin = {

        /**
         * Fix to a core bug: notCalculatedMessage does not have translation applied.
         *
         * @returns {mixin}
         */
        initialize: function () {
            this._super();
            this.notCalculatedMessage = $t('Not yet calculated');

            return this;
        }

    };

    /**
     * Mixin of Magento_Tax/js/view/checkout/summary/tax
     */
    return function (target) {
        return target.extend(mixin);
    };
});
