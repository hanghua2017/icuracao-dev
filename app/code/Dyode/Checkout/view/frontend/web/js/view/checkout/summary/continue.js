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
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/step-navigator',
    'Dyode_CheckoutDeliveryMethod/js/model/delivery-save-processor',
    'Dyode_CheckoutAddressStep/js/model/address-validator',
    'Dyode_CheckoutAddressStep/js/model/estimate-shipping-processor',
    'Dyode_Checkout/js/view/model/shipping-info-save-processor'
], function (
    $,
    Component,
    registry,
    quote,
    stepNavigator,
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
        }
    });
});
