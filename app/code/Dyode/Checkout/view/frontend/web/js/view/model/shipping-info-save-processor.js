/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout shipping core js file
 *
 * @module    Dyode_Checkout
 * @author    Mathew Joseph <mathew.joseph@dyode.com>
 * @copyright Copyright © Dyode
 */

/**
 * Deals with shipping data information saving while we click on "next"
 * button in shipping step. Magento uses default processor to do this.
 * We are adding this processor in order to fire a custom api call instead
 * of Magento's default api call.
 *
 * Why we need a custom api call? This is because, all the shipping calculations
 * are here based on quote item based instead of the quote. So basically we need
 * multi-shipping inside onepage-checkout!!!
 *
 * Carrier and shipping method we are using is flatrate. This is because Magento always
 * expect a shipping method against quote for it's internal working. So this is for
 * bypassing it.
 */

'use strict';

define([
    'ko',
    'Magento_Checkout/js/model/quote',
    'mage/storage',
    'Magento_Checkout/js/model/payment-service',
    'Magento_Checkout/js/model/payment/method-converter',
    'Magento_Checkout/js/model/error-processor',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/select-billing-address',
    'Magento_Checkout/js/model/shipping-save-processor/payload-extender',
    'Dyode_Checkout/js/view/model/shipping-data-provider',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Customer/js/model/customer'
], function (
    ko,
    quote,
    storage,
    paymentService,
    methodConverter,
    errorProcessor,
    fullScreenLoader,
    selectBillingAddressAction,
    payloadExtender,
    shippingInfoDataProvider,
    urlBuilder,
    customer
) {
    return {

        /**
         * Save shipping information.
         *
         * shipping_carrier_info is an additional payload we are adding in order
         * to hold quote item based shipping information.
         */
        saveShippingInformation: function () {
            var payload,
                shippingData = shippingInfoDataProvider.shippingInfo();

            if (!quote.billingAddress()) {
                selectBillingAddressAction(quote.shippingAddress());
            }
            payload = {
                addressInformation: {
                    shipping_address: quote.shippingAddress(),
                    billing_address: quote.billingAddress(),
                    shipping_method_code: 'flatrate',
                    shipping_carrier_code: 'flatrate',
                    shipping_carrier_info: shippingData
                }
            };

            payloadExtender(payload);

            fullScreenLoader.startLoader();

            return storage.post(
                this.getUrlForSetShippingInformation(),
                JSON.stringify(payload)
            ).done(
                function (response) {
                    quote.setTotals(response.totals);
                    paymentService.setPaymentMethods(methodConverter(response['payment_methods']));
                    fullScreenLoader.stopLoader();
                }
            ).fail(
                function (response) {
                    errorProcessor.process(response);
                    fullScreenLoader.stopLoader();
                }
            );
        },

        /**
         * Preparing api url for saving shipping information.
         * This is a custom api call.
         */
        getUrlForSetShippingInformation: function () {

            var requestInfo = {
                url: '/carts/mine/custom-shipping-info',
                params: {}
            };

            if (!customer.isLoggedIn()) {
                requestInfo = {
                    url: '/guest-carts/:cartId/custom-shipping-info',
                    params: {
                        cartId: quote.getQuoteId()
                    }
                };
            }

            return urlBuilder.createUrl(requestInfo.url, requestInfo.params);
        }

    };
});
