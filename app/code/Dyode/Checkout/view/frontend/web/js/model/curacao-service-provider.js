/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout core module
 *
 * @module    Dyode_Checkout
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define([
    'jquery',
    'ko',
    'mage/storage',
    'mage/url',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/url-builder'
], function ($, ko, storage, Url, customer, quote, fullScreenLoader, urlBuilder) {

    var curacaoPaymentInfo = window.checkoutConfig.curacaoPayment,
        isUserLinked = !!curacaoPaymentInfo.linked;

    return {
        isResponseError: ko.observable(false),
        response: ko.observable(null),
        message: ko.observable(''),
        isUserLinked: ko.observable(isUserLinked),

        /**
         * Checks the given curacao details are valid or not.
         *
         * @param {Object} curacaoInfo
         * @returns {Deferred}
         */
        verifyCuracaoId: function (curacaoInfo) {
            var self = this;

            curacaoInfo.isAjax = true;
            fullScreenLoader.startLoader();

            return $.ajax({
                url: this.verifyCuracaoIdUrl(),
                type: 'POST',
                dataType: 'json',
                data: curacaoInfo,

                /**
                 * Success
                 * @param {JSON} result
                 */
                success: function (result) {
                    fullScreenLoader.stopLoader();

                    if (result.type === 'error') {
                        self.isResponseError(true);
                        self.message(result.message);
                    } else {
                        self.isResponseError(false);
                        self.response(result.data);
                    }
                },

                /**
                 * Some bad thing happend in Ajax request
                 */
                error: function () {
                    fullScreenLoader.stopLoader();
                    self.isResponseError(true);
                    self.response(null);
                },

                /**
                 * Ajax request complete
                 */
                complete: function () {
                    fullScreenLoader.stopLoader();
                }
            });
        },

        /**
         * Send sms to curacao user.
         */
        sendSMS: function () {
            var self = this;
            //userInfo.isAjax = true;

            fullScreenLoader.startLoader();

            return $.ajax({
                url: this.smsUrl(),
                type: 'POST',
                success: function (result) {
                    fullScreenLoader.stopLoader();

                    if (result.type === 'error') {
                        self.isResponseError(true);
                        self.message(result.message);
                    } else {
                        self.isResponseError(false);
                        self.response(result.data);
                    }
                },

                /**
                 * Some bad thing happend in Ajax request
                 */
                error: function () {
                    fullScreenLoader.stopLoader();
                    self.isResponseError(true);
                    self.response(null);
                },

                /**
                 * Ajax request complete
                 */
                complete: function () {
                    fullScreenLoader.stopLoader();
                }
            });
        },

        /**
         * Call curacao user.
         */
        placeCall: function () {
            var self = this;

            fullScreenLoader.startLoader();

            return $.ajax({
                url: this.callUrl(),
                type: 'POST',
                success: function (result) {
                    fullScreenLoader.stopLoader();

                    if (result.type === 'error') {
                        self.isResponseError(true);
                        self.message(result.message);
                    } else {
                        self.isResponseError(false);
                        self.response(result.data);
                    }
                },

                /**
                 * Some bad thing happend in Ajax request
                 */
                error: function () {
                    fullScreenLoader.stopLoader();
                    self.isResponseError(true);
                    self.response(null);
                },

                /**
                 * Ajax request complete
                 */
                complete: function () {
                    fullScreenLoader.stopLoader();
                }
            });
        },

        /**
         * Checks the given code is correct for the curacao user.
         *
         * @param {Json} codeInfo
         */
        scrutinizeVerifyCode: function (codeInfo) {
            var self = this;

            codeInfo.isAjax = true;
            fullScreenLoader.startLoader();

            return $.ajax({
                url: this.scrutinizeVerifyCodeUrl(),
                type: 'POST',
                dataType: 'json',
                data: codeInfo,

                /**
                 * Success
                 * @param {JSON} result
                 */
                success: function (result) {
                    if (result.type === 'error') {
                        self.isResponseError(true);
                        self.message(result.message);
                        self.isUserLinked(false);
                    } else {
                        self.isResponseError(false);
                        self.response(result.data);
                        self.isUserLinked(true);
                    }
                },

                /**
                 * Some bad thing happend in Ajax request
                 */
                error: function () {
                    self.isResponseError(true);
                    self.response(null);
                    fullScreenLoader.stopLoader();
                }
            });
        },

        /**
         * Url for curacao user verifying via code request.
         */
        scrutinizeVerifyCodeUrl: function () {
            return Url.build('dyode_checkout/curacao/codeverify');
        },

        /**
         * Scrutinize user information in order to make sure user is a valid curacao user.
         *
         * @param  {Object} userInfo
         * @returns {Deferred}
         */
        scrutinizeCuracaoUser: function (userInfo) {
            var self = this;

            userInfo.isAjax = true;
            fullScreenLoader.startLoader();

            return $.ajax({
                url: this.scrutinizeCuracaoUserUrl(),
                type: 'POST',
                dataType: 'json',
                data: userInfo,

                /**
                 * Success
                 * @param {JSON} result
                 */
                success: function (result) {
                    if (result.type === 'error') {
                        self.isResponseError(true);
                        self.message(result.message);
                        self.isUserLinked(false);
                    } else {
                        self.isResponseError(false);
                        self.response(result.data);
                        self.isUserLinked(true);
                    }
                },

                /**
                 * Some bad thing happend in Ajax request
                 */
                error: function () {
                    self.isResponseError(true);
                    self.response(null);
                    fullScreenLoader.stopLoader();
                }
            });
        },

        /**
         * Apply curacao credit in the payment total collection
         *
         * @returns {Deferred}
         */
        collectCuracaoTotals: function () {
            fullScreenLoader.startLoader();

            return storage.get(
                this.getApplyCuracaoTotalsUrl()
            ).done(function (response) {
                quote.setTotals(response.totals);
            }).always(function () {
                fullScreenLoader.stopLoader();
            });
        },

        /**
         * Remove curacao credit in the payment total collection
         *
         * @returns {Deferred}
         */
        removeCuracaoTotals: function () {
            fullScreenLoader.startLoader();

            return storage.get(
                this.getRemoveCuracaoTotalsUrl()
            ).done(function (response) {
                quote.setTotals(response.totals);
            }).always(function () {
                fullScreenLoader.stopLoader();
            });
        },

        /**
         * Verify Curacao id request url.
         *
         * @returns {String}
         */
        verifyCuracaoIdUrl: function () {
            return Url.build('dyode_checkout/curacao/verify');
        },

        /**
         * Scrutinize curacao user request url
         *
         * @returns {*}
         */
        scrutinizeCuracaoUserUrl: function () {
            return Url.build('dyode_checkout/curacao/scrutinize');
        },

        /**
         * SMS sending URL
         */
        smsUrl: function () {
            return Url.build('dyode_checkout/curacao/phoneverify');
        },

        /**
         * SMS sending URL
         */
        callUrl: function () {
            return Url.build('dyode_checkout/curacao/phonecall');
        },

        /**
         * Url to for apply curacao credit api call.
         *
         * @returns {String}
         */
        getApplyCuracaoTotalsUrl: function () {

            var requestInfo = {
                url: '/carts/mine/curacao/collect-totals',
                params: {}
            };

            if (!customer.isLoggedIn()) {
                requestInfo = {
                    url: '/guest-carts/:cartId/curacao/collect-totals',
                    params: {
                        cartId: quote.getQuoteId()
                    }
                };
            }

            return urlBuilder.createUrl(requestInfo.url, requestInfo.params);
        },

        /**
         * Url to for remove curacao credit api call.
         *
         * @returns {String}
         */
        getRemoveCuracaoTotalsUrl: function () {

            var requestInfo = {
                url: '/carts/mine/curacao/remove-totals',
                params: {}
            };

            if (!customer.isLoggedIn()) {
                requestInfo = {
                    url: '/guest-carts/:cartId/curacao/remove-totals',
                    params: {
                        cartId: quote.getQuoteId()
                    }
                };
            }

            return urlBuilder.createUrl(requestInfo.url, requestInfo.params);
        }
    };
});
