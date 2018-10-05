/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       06/09/2018
 */

/**
 * Deals with shipping data information saving while we click on "next"
 * button in shipping step. Magento uses default processor to do this.
 * We are adding this processor in order to fire a custom api call instead
 * of Magento's default api call.
 * 
 * Why we need a custom api call? This is because, all the shipping calculations
 * are here based on quote item based instead of the quote. So basically we need
 * multi-shipping inside onpage-checkout!!!
 */
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
    './shipping-data-provider',
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
        'use strict';

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
                        shipping_method_code: null, //keeping this for legacy
                        shipping_carrier_code: null, //keeping this for legacy
                        shipping_carrier_info: shippingData
                    }
                }

                payloadExtender(payload);

                fullScreenLoader.startLoader();

                return storage.post(
                    this.getUrlForSetShippingInformation(quote),
                    JSON.stringify(payload)
                ).done(
                    function (response) {
                        quote.setTotals(response.totals);
                        paymentService.setPaymentMethods(
                            methodConverter(response['payment_methods']
                        ));
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
            getUrlForSetShippingInformation: function (quote) {

                var requestInfo = {
                    url: '/carts/mine/custom-shipping-info',
                    params: {}
                }

                if (!customer.isLoggedIn()) {
                    requestInfo = {
                        url: '/guest-carts/:cartId/custom-shipping-info',
                        params: {
                            cartId: quote.getQuoteId()
                        }
                    }
                }

                return urlBuilder.createUrl(requestInfo.url, requestInfo.params);
            },

        };
    });
