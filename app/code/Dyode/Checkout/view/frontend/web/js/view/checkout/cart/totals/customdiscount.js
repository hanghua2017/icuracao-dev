/**
 * Dyode_CheckoutAddressStep Magento2 Module.
 *
 * Adding new checkout step in the one page checkout.
 *
 * @module    Dyode_CheckoutAddressStep
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define([
    'ko',
    'jquery',
    'underscore',
    'Magento_Checkout/js/view/summary/abstract-total',
    'Magento_Checkout/js/model/quote',
    'Dyode_Checkout/js/model/curacao-service-provider'
], function (ko, $, _, Component, quote, curacaoServiceProvider) {

    var initialDownPayment = window.checkoutConfig.curacaoPayment.total;

    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/checkout/summary/customdiscount'
        },
        totals: quote.getTotals(),
        value: ko.observable(initialDownPayment),

        /**
         * Subscribe to quote totals so that any update on curacao credit,
         * which will be fetched and changed in frontend.
         */
        initialize: function () {
            var self = this;

            this._super();

            quote.totals.subscribe(function (newTotals) {
                var price = 0,
                    curacaoTotalSegment;

                if (newTotals['total_segments']) {
                    curacaoTotalSegment = _.findWhere(newTotals['total_segments'], {
                        code: 'curacao_discount'
                    });

                    if (curacaoTotalSegment && curacaoTotalSegment.value) {
                        price = curacaoTotalSegment.value;
                    }
                }

                self.value(self.getFormattedPrice(price));
            });

            return this;
        },

        /**
         * Shows only in payment step and only if the user is a curacao account holder.
         *
         * @override
         */
        isDisplayed: function () {
            return this.isFullMode() && curacaoServiceProvider.isUserLinked();
        }
    });
});
