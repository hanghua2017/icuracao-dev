/**
 * Dyode_Checkout Magento 2 module
 * Extending Magento_Checkout
 * @module  Dyode_Chekout
 * @author  Kavitha <kavitha@dyode.com>
 * @copyright Copyright © Dyode
 */
define([
    'uiComponent',
    'mage/storage',
    'mage/url',
    'Magento_Checkout/js/model/step-navigator',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Dyode_CheckoutDeliveryMethod/js/data/delivery-data-provider'
], function (
    Component,
    storage,
    Url,
    stepNavigator,
    errorProcessor,
    fullScreenLoader,
    deliveryDataProvider
) {
    'use strict';

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
            console.log(this);
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
         * @returns {*}
         */
        saveDeliveryOptions: function () {
            var payload = deliveryDataProvider.getDeliveryData(),
                deliverySaveUrl = Url.build('/delivery-option/384837483jjhdfh838/save');

            fullScreenLoader.startLoader();

            return storage.post(
                deliverySaveUrl,
                JSON.stringify(payload)
            ).done(
                function (response) {
                    fullScreenLoader.stopLoader();
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                    fullScreenLoader.stopLoader();
                }
            );

        }
    });
});
