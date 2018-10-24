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
    'Dyode_Checkout/js/model/curacao-service-provider',
], function (ko, $, _, Component, quote, curacaoServiceProvider) {

    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/checkout/summary/customdiscount'
        },
        totals: quote.getTotals(),

        /**
         * Shows only in payment step and only if the user is a curacao account holder.
         *
         * @override
         */
        isDisplayed: function () {
            return this.isFullMode() && curacaoServiceProvider.isUserLinked();
        },

        /**
         * Collect curacao total from the total segment.
         * @returns {String}
         */
        getCustomdiscountTotal: function () {
            var price = 0,
                curacaoTotalSegment;

            if (this.totals() && this.totals()['total_segments']) {
                curacaoTotalSegment = _.findWhere(this.totals()['total_segments'], {
                    code: 'curacao_discount'
                });

                if (curacaoTotalSegment && curacaoTotalSegment.value) {
                    price = curacaoTotalSegment.value;
                }
            }

            return this.getFormattedPrice(price);
        }
    });
});
