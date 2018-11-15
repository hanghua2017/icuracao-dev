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
    'uiRegistry',
    'uiComponent',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Authorizenet/js/view/payment/method-renderer/authorizenet-directpost',
    'Dyode_CuracaoPayment/js/view/payment/curacaofullpayment',
    'Dyode_Checkout/js/data/curacao-data-provider',
    'Dyode_CheckoutDeliveryMethod/js/model/delivery-save-processor',
    'Dyode_CheckoutAddressStep/js/model/address-validator',
    'Dyode_CheckoutAddressStep/js/model/estimate-shipping-processor',
    'Dyode_Checkout/js/view/model/shipping-info-save-processor'
], function (
    $,
    _,
    registry,
    Component,
    quote,
    stepNavigator,
    authorizePaymentMethod,
    CuracaoPaymentMethod,
    curacaoDataProvider,
    deliverySaveProcessor,
    addressValidator,
    shippingEstimateProcessor,
    shippingSaveProcessor
) {

    /**
     * Sidebar Continue Button Component
     */

    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/checkout/summary/continue'
        },
        checkMoPlaceOrderName: 'checkout.steps.billing-step.payment.payments-list.checkmo',
        authorizePlaceOrderName: 'checkout.steps.billing-step.payment.payments-list.authnetcim',
        curacaoCustomPaymentName: 'checkout.steps.billing-step.payment.payments-list.curacaofullpayment',

        /**
         * Subscribe to quote.totals and update zeroDownPayment flag.
         *
         * @returns {exports}
         */
        initialize: function () {
            var self = this;

            this._super();

            quote.totals.subscribe(function (newTotals) {
                curacaoDataProvider.isZeroDownPayment(self.isZeroDownPayment(newTotals));
            });

            return this;
        },

        /**
         * Proceeds to the next step
         */
        navigateToNextStep: function () {
            $("#discount-code-error").remove();
            this.performAjaxUpdates().done(
                function () {
                    stepNavigator.next();
                }
            );
        },

        /**
         * Place Order either with authorize.net or with Curacao payment.
         * @returns {*}
         */
        placeOrder: function () {
            if (curacaoDataProvider.canPerformCuracaoPayment()) {
                return this.placeOrderWithCuracaoPayment();
            }

            return this.placeOrderWithAuthorize();
        },

        /**
         * do background works related to the active step
         */
        performAjaxUpdates: function () {
            var activeStepIndex = stepNavigator.getActiveItemIndex(),
                steps = stepNavigator.steps(),
                activeStep = steps[activeStepIndex];

            if (activeStep.code === 'deliverySelection') {
                return deliverySaveProcessor.saveDeliveryOptions();
            }

            if (activeStep.code === 'address-step') {
                if (addressValidator.validateAddresses()) {
                    return shippingEstimateProcessor.estimateShippingMethods(quote.shippingAddress());
                }
            }

            if (activeStep.code === 'shipping') {
                return shippingSaveProcessor.saveShippingInformation();
            }

            return $.Deferred();
        },

        /**
         * Check if current step is payment step
         */
        isPayment: function () {
            var activeStepIndex = stepNavigator.getActiveItemIndex(),
                steps = stepNavigator.steps(),
                activeStep = steps[activeStepIndex];

            return activeStep.code === 'payment';
        },

        /**
         * Place order with Authorize.net
         *
         * @returns {Boolean}
         */
        placeOrderWithAuthorize: function () {
            return registry.get(this.authorizePlaceOrderName).placeOrder();
        },


        /**
         * Place order with Curacao custom payment option.
         *
         * @returns {Boolean}
         */
        placeOrderWithCuracaoPayment: function () {
            return registry.get(this.curacaoCustomPaymentName).placeOrder();
        },

        /**
         * Verify whether the curacao down payment is zero.
         *
         * @param {Object} totals
         * @returns {Boolean}
         */
        isZeroDownPayment: function (totals) {
            var isDownPayment = false,
                curacaoTotalSegment;

            if (totals['total_segments']) {
                curacaoTotalSegment = _.findWhere(totals['total_segments'], {
                    code: 'curacao_discount'
                });

                if (curacaoTotalSegment && curacaoTotalSegment.value == 0) {
                    isDownPayment = true;
                }
            }

            return isDownPayment;
        }
    });
});
