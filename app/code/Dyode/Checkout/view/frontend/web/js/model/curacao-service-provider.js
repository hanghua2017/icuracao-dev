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
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Ui/js/model/messageList'
], function ($, ko, storage, Url, fullScreenLoader, messageList) {

    return {
        isResponseError: ko.observable(false),
        response: ko.observable(null),

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
                        messageList.addErrorMessage({
                            message: result.message
                        });
                    } else {
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
        }
    };
});
