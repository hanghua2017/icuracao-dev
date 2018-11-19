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

define(['ko'], function (ko) {

    var mixin = {

        /**
         * Make the authorize.net payment method always checked.
         *
         * @returns {String}
         */
        isChecked: ko.computed(function () {
            return 'authnetcim';
        })
    };

    /**
     * Mixin of ParadoxLabs_Authnetcim/js/view/payment/method-renderer/authnetcim
     */
    return function (target) {
        return target.extend(mixin);
    };
});
