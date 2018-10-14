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
    'uiComponent',
    'uiRegistry',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Dyode_CheckoutDeliveryMethod/js/data/delivery-data-provider',
    'Dyode_CheckoutAddressStep/js/model/address-validator',
    'Dyode_CheckoutAddressStep/js/model/estimate-shipping-processor',
    'Dyode_Checkout/js/view/model/shipping-info-save-processor'
], function (
    $,
    Component,
    registry,
    Url,
    quote,
    stepNavigator,
    errorProcessor,
    fullScreenLoader,
    deliveryDataProvider,
    addressValidator,
    shippingEstimateProcessor,
    shippingSaveProcessor
) {
    var quoteItemData = window.checkoutConfig.quoteItemData;

    /**
     * Sidebar Continue Button Component
     */

    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/checkout/summary/continue'
        },
        checkMoPlaceOrderName: 'checkout.steps.billing-step.payment.payments-list.checkmo',
        authorizePlaceOrderName: 'checkout.steps.billing-step.payment.payments-list.authorizenet_directpost',

        /**
         * Proceeds to the next step
         */
        navigateToNextStep: function () {
            this.performAjaxUpdates().done(
                function () {
                    stepNavigator.next();
                }
            );
        },

        /**
         * Place order.
         * We are triggering the default place order button to avoid further chaos.
         * We are expecting only authorize.net payment here. Check/money order payment method is for testing purpose.
         * @todo if we can create a stand alone place order button component that performs the exact same functionality
         *       of the default place order button, then it would be better.
         */
        placeOrder: function () {
            var checkMoBtnComponent = registry.get(this.checkMoPlaceOrderName),
                authorizeBtnComponent = registry.get(this.authorizePlaceOrderName);

            if (authorizeBtnComponent) {
                authorizeBtnComponent.placeOrder();
            }

            if (checkMoBtnComponent) {
                checkMoBtnComponent.placeOrder();
            }

            return true;
        },

        /**
         * do background works related to the active step
         */
        performAjaxUpdates: function () {
            var activeStepIndex = stepNavigator.getActiveItemIndex(),
                steps = stepNavigator.steps(),
                activeStep = steps[activeStepIndex];

            if (activeStep.code === 'deliverySelection') {
                return this.saveDeliveryOptions();
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
         * Save delivery options against the quote when user proceed from delivery step -> address step
         * @todo This needs to be done through a save processor.
         */
        saveDeliveryOptions: function () {
            // Better error handling # TODO Quote item id repeats
            var payload = deliveryDataProvider.getDeliveryData(),
                cartId = quoteItemData[0].quote_id;

            fullScreenLoader.startLoader();

            return $.ajax({
                url: Url.build('delivery-step/deliveryMethods/save'),
                type: 'POST',
                dataType: 'json',
                data: {
                    quoteItemsData: JSON.stringify(payload),
                    quoteId: cartId
                },

                /**
                 * Success
                 * @param {JSON} response
                 */
                success: function (response) {
                    fullScreenLoader.stopLoader();
                },

                /**
                 * Some bad thing happend in Ajax request
                 */
                error: function () {
                    fullScreenLoader.stopLoader();
                },

                /**
                 * Ajax request complete
                 */
                complete: function () {
                    fullScreenLoader.stopLoader();
                }
            });

        }
    });
});
