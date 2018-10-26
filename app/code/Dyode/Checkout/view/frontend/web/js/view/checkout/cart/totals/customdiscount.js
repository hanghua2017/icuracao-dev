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
    'Magento_Customer/js/model/customer',
    'Dyode_Checkout/js/model/curacao-service-provider',
    'Dyode_Checkout/js/data/curacao-data-provider'
], function (ko, $, _, Component, quote, customer, curacaoServiceProvider, curacaoDataProvider) {

    var curacaoPaymentInfo = window.checkoutConfig.curacaoPayment,
        initialDownPayment = curacaoPaymentInfo.total,
        initialDPaymentNaked = parseInt(curacaoPaymentInfo.totalNaked, 10);

    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/checkout/summary/customdiscount'
        },
        totals: quote.getTotals(),
        value: ko.observable(initialDownPayment),
        valueNaked: ko.observable(initialDPaymentNaked),
        isCustomerLoggedIn: customer.isLoggedIn(),

        /**
         * Subscribe to quote totals so that any update on curacao credit,
         * which will be fetched and changed in frontend.
         */
        initialize: function () {
            var self = this;

            this._super();

            quote.totals.subscribe(function (newTotals) {
                var price = self.getCuracaoCreditByTotals(newTotals),
                    formattedPrice = self.getFormattedPrice(price);

                self.valueNaked(parseInt(price, 10));
                self.value(formattedPrice);
            });

            return this;
        },

        /**
         * Shows only in payment step and only if the user is a curacao account holder.
         *
         * @override
         */
        isDisplayed: function () {
            return this.isFullMode() &&
                curacaoServiceProvider.isUserLinked() &&
                curacaoDataProvider.hasCuracaoCreditApplied();
        },

        /**
         * Collect curacao credit from the quote totals segment.
         *
         * @param {Object} totals
         * @returns {Number} price
         */
        getCuracaoCreditByTotals: function (totals) {
            var price = 0,
                curacaoTotalSegment;

            if (totals['total_segments']) {
                curacaoTotalSegment = _.findWhere(totals['total_segments'], {
                    code: 'curacao_discount'
                });

                if (curacaoTotalSegment && curacaoTotalSegment.value) {
                    price = curacaoTotalSegment.value;
                }
            }

            return price;
        }
    });
});
