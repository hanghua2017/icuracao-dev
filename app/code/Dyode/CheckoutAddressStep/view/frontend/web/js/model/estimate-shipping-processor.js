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
        'jquery',
        'mageUtils',
        'mage/storage',
        'Magento_Customer/js/model/customer',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data',
        'Magento_Checkout/js/model/url-builder',
        'Magento_Checkout/js/model/shipping-service',
        'Magento_Checkout/js/model/shipping-rate-registry',
        'Magento_Checkout/js/model/error-processor',
        'Magento_Checkout/js/action/select-billing-address'
    ], function (
        $,
        utils,
        storage,
        customer,
        quote,
        checkoutData,
        urlBuilder,
        shippingService,
        rateRegistry,
        errorProcessor,
        selectBillingAddressAction
    ) {

        return {

            /**
             * Magento by default estimate shipping methods whenever the zipcode in the shipping address changes.
             * For this, Magento uses rate processors. We can register any number of processors. Here we registered
             * a custom processor (i.e, this component) whenever Magento process "new-customer-address" type address.
             * This way we will avoid Magento's default estimation processing.
             *
             * @param {Object} address
             */
            getRates: function (address) {
                this.estimateShippingMethods(address);
            },

            /**
             * Estimate shipping methods based on the zip code.
             *
             * @param {Object} address
             * @return {Deferred}
             */
            estimateShippingMethods: function (address) {
                var serviceUrl, payload;

                if (!address || !address.postcode) {
                    return $.Deferred();
                }

                if (!quote.billingAddress()) {
                    selectBillingAddressAction(quote.shippingAddress());
                }

                shippingService.isLoading(true);
                serviceUrl = this.estimateShippingMethodsUrl();
                payload = JSON.stringify({
                    address: {
                        zip_code: address.postcode
                    }
                });

                return storage.post(
                    serviceUrl, payload, false
                ).done(function (result) {
                    shippingService.setShippingRates(result);
                }).fail(function (response) {
                    shippingService.setShippingRates([]);
                    errorProcessor.process(response);
                }).always(function () {
                    shippingService.isLoading(false);
                });
            },

            /**
             * @return {*}
             */
            estimateShippingMethodsUrl: function () {
                var params = this.getCheckoutMethod() == 'guest' ? //eslint-disable-line eqeqeq
                    {
                        quoteId: quote.getQuoteId()
                    } : {},
                    urls = {
                        'guest': '/guest-carts/:quoteId/update-shipping-methods',
                        'customer': '/carts/mine/update-shipping-methods'
                    };

                return this.getUrl(urls, params);
            },

            /**
             * Get url for service.
             *
             * @param {*} urls
             * @param {*} urlParams
             * @return {String|*}
             */
            getUrl: function (urls, urlParams) {
                var url;

                if (utils.isEmpty(urls)) {
                    return 'Provided service call does not exist.';
                }

                if (!utils.isEmpty(urls['default'])) {
                    url = urls['default'];
                } else {
                    url = urls[this.getCheckoutMethod()];
                }

                return urlBuilder.createUrl(url, urlParams);
            },

            /**
             * @return {String}
             */
            getCheckoutMethod: function () {
                return customer.isLoggedIn() ? 'customer' : 'guest';
            }
        };
    }
);
