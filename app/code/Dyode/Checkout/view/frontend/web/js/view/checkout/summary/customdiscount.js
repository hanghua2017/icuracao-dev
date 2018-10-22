/**
 * Dyode_Checkout Magento 2 module
 * Extending Magento_Checkout
 * @module  Dyode_Chekout
 * @author  Kavitha <kavitha@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define([
        'jquery',
        'underscore',
        'Magento_Checkout/js/view/summary/abstract-total',
        'Magento_Checkout/js/model/quote'
    ], function ($, _, Component, quote) {

        return Component.extend({
            defaults: {
                template: 'Dyode_Checkout/checkout/summary/customdiscount'
            },
            totals: quote.getTotals(),

            /**
             * @returns {Boolean}
             */
            isDisplayedCustomdiscountTotal: function () {
                return true;
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
    }
);
