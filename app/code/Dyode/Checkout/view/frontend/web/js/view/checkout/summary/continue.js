/**
 * Dyode_Checkout Magento 2 module
 * Extending Magento_Checkout
 * @module  Dyode_Chekout
 * @author  Kavitha <kavitha@dyode.com>
 * @copyright Copyright © Dyode
 */
define([
    'jquery',
    'uiComponent',
    'mage/url',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Dyode_CheckoutDeliveryMethod/js/data/delivery-data-provider',
    'jquery'
], function (
    $,
    Component,
    Url,
    stepNavigator,
    errorProcessor,
    fullScreenLoader,
    deliveryDataProvider
) {
    'use strict';

    var quoteItemData = window.checkoutConfig.quoteItemData;
    /**
     * Sidbar Continue Button Component
     */
    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/checkout/summary/continue'
        },

        /**
         * Proceeds to the next step
         */
        navigateToNextStep: function () {

            this.performAjaxUpdates();
            stepNavigator.next();
        },

        /**
         * do background works related to the active step
         */
        performAjaxUpdates: function () {
            var activeStepIndex = stepNavigator.getActiveItemIndex(),
                steps = stepNavigator.steps(),
                activeStep = steps[activeStepIndex];

            if (activeStep.code === 'deliverySelection') {
                this.saveDeliveryOptions();
            }
        },

        /**
         * Save delivery options against the quote when user proceed from delivery step -> address step
         */
        saveDeliveryOptions: function () {
            // Better error handling # TODO Quote item id repeats
            var payload = deliveryDataProvider.getDeliveryData(),
                cartId = quoteItemData[0].quote_id;

            fullScreenLoader.startLoader();

            $.ajax({
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
                    console.log('Error')
                },

                /**
                 * Ajax request complete
                 */
                complete: function () {
                    fullScreenLoader.stopLoader();
                    console.log('Completed');
                }
            });

        }
    });
});
