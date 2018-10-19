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
    'Magento_Checkout/js/model/full-screen-loader'
], function ($, ko, storage, Url, fullScreenLoader) {

    return {
        isResponseError: ko.observable(false),
        response: ko.observable(null),
        message: ko.observable(''),

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
        }
    };
});
