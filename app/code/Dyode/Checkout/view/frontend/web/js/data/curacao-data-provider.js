/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout core module
 *
 * @module    Dyode_Checkout
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define(['ko'], function (ko) {
    var isUserLinked = !!window.checkoutConfig.curacaoPayment.linked;

    return {
        hasCuracaoCreditApplied: ko.observable(isUserLinked),
        isZeroDownPayment: ko.observable(false),

        /**
         * Checks whether payment option has to be curacao-custom-payment method.
         *
         * Curacao-payment-method will be used only if when a curacao user opted curacao credit as payment
         * option and the user has a zero down payment.
         *
         * @returns {Boolean}
         */
        canPerformCuracaoPayment: function () {
            return this.hasCuracaoCreditApplied() && this.isZeroDownPayment();
        }
    };
});
