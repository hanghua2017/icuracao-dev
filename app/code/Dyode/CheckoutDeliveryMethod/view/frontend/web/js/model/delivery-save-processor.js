/**
 * Dyode_CheckoutDeliveryMethod Magento2 Module.
 *
 * Add a new checkout step in checkout
 *
 * @module    Dyode_CheckoutDeliveryMethod
 * @copyright Copyright © Dyode
 * @author Rajeev K Tomy <rajeev.ktomy@dyode.com>
 */

'use strict';

define([
    'jquery',
    'underscore',
    'mage/url',
    'mage/translate',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Dyode_CheckoutDeliveryMethod/js/data/delivery-data-provider'
], function ($, _, Url, $t, MessageList, errorProcessor, fullScreenLoader, deliveryDataProvider) {

    return {
        quoteItemData: window.checkoutConfig.quoteItemData,
        deliveryInfo: deliveryDataProvider.getDeliveryData(),

        /**
         * Validating delivery options.
         *
         * For each quote item, there should be a delivery option selected.
         * If the delivery option selected is "Store Pickup", then there should be a "store" associated with it.
         *
         * @returns {Boolean}
         */
        validateDeliveryOptions: function () {
            var storePickupOptions = _.where(this.deliveryInfo, {
                deliveryType: 'store_pickup',
                storeId: false
            });

            //this means, there is a store pickup option and it has no store associated.
            if (storePickupOptions.length > 0) {
                MessageList.addErrorMessage({
                    message: $t('Please select a store to proceed.')
                });

                return false;
            }

            return true;
        },

        /**
         * Save delivery options against the quote when user proceed from delivery step -> address step
         * @todo We are using a controller to handle delivery saving. It will be better if we can use an api for this.
         */
        saveDeliveryOptions: function () {
            var payload = this.deliveryInfo,
                cartId = this.quoteItemData[0].quote_id;

            if (this.validateDeliveryOptions()) {
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

            return $.Deferred();
        }
    };
});
