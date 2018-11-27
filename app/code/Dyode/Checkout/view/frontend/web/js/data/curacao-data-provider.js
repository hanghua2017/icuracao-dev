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

    var curacaoPaymentInfo = window.checkoutConfig.curacaoPayment,
        isUserLinked = !!curacaoPaymentInfo.linked,
        canCuracaoCreditCharged = curacaoPaymentInfo.canCharge,
        initialDownPayment = curacaoPaymentInfo.total;

    return {
        hasCuracaoCreditApplied: ko.observable(isUserLinked && canCuracaoCreditCharged),
        isZeroDownPayment: ko.observable(false),
        downPayment: ko.observable(initialDownPayment),

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
