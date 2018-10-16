/**
 * Dyode_Checkout Module
 *
 * Extending Magento_Checkout core module
 *
 * @module    Dyode_Checkout
 * @author    Kavitha <kavitha@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define([
    'jquery',
    'ko',
    'mage/url',
    'mage/storage',
    'mage/translate',
    'Magento_Ui/js/form/form',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Customer/js/model/customer',
    'Magento_Ui/js/modal/modal'
], function (
    $,
    ko,
    Url,
    storage,
    $t,
    Component,
    messageList,
    quote,
    urlBuilder,
    fullScreenLoader,
    getPaymentInformationAction,
    totals,
    customer
) {
    var curacaoPaymentInfo = window.checkoutConfig.curacaoPayment;

    return Component.extend({

        isLoggedIn: customer.isLoggedIn(),
        customerData: window.customerData,
        downPayment: curacaoPaymentInfo.downPayment,
        limit: curacaoPaymentInfo.limit,
        canApply: curacaoPaymentInfo.canApply,
        linked: curacaoPaymentInfo.linked,
        curacaoAccountVerifyModalTemplate: 'Dyode_Checkout/custom-form/account-verify-modal',
        curacaoAccountVerifyModal: null,
        smsIconUrl: curacaoPaymentInfo.mediaUrl + '/images/sms-icon.png',
        callIconUrl: curacaoPaymentInfo.mediaUrl + '/images/call-icon.png',
        verificationCodeInpValue: ko.observable(''),
        ssnVerifyInpValue: ko.observable(''),
        dateOfBirthInpValue: ko.observable(''),
        zipCodeInpValue: ko.observable(''),
        maidenNameInpValue: ko.observable(''),

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                .observe({
                    ApplyDiscount: ko.observable(true)
                });

            this.ApplyDiscount.subscribe(function (newValue) {
                if (newValue) {
                    this.getDiscount();
                } else {
                    this.removeDiscount();
                }
            }, this);

            return this;
        },

        getLinkUrl: function () {
            return Url.build('creditapp/credit/index');
        },

        getDiscount: function () {
            var message = $t('Your store credit was successfully applied');

            messageList.clear();
            fullScreenLoader.startLoader();

            return storage.post(
                urlBuilder.createUrl('/checkout/apply', {})
            ).done(function (response) {
                var deferred;

                if (response) {
                    deferred = $.Deferred();
                    totals.isLoading(true);
                    getPaymentInformationAction(deferred);
                    $.when(deferred).done(function () {
                        totals.isLoading(false);
                    });
                    messageList.addSuccessMessage({
                        'message': message
                    });
                }
            }).always(function () {
                fullScreenLoader.stopLoader();
            });
        },

        removeDiscount: function () {
            fullScreenLoader.startLoader();

            return storage.delete(
                urlBuilder.createUrl('/checkout/remove', {})
            ).done(
                function (response) {
                    var deferred;

                    if (response) {
                        deferred = $.Deferred();
                        totals.isLoading(true);
                        getPaymentInformationAction(deferred);
                        $.when(deferred).done(function () {
                            totals.isLoading(false);
                            fullScreenLoader.stopLoader();
                        });
                        messageList.addSuccessMessage({
                            'message': message
                        });
                    }
                }
            ).fail(
                function (response) {
                    //   alert(response);
                }
            ).always(function () {
                fullScreenLoader.stopLoader();
            });
        },

        getGuestEmail: function () {
            if (quote.guestEmail) {
                return quote.guestEmail;
            }

            var storageDetails = localStorage.getItem('mage-cache-storage');
            var checkoutDetails = JSON.parse(storageDetails);
            var email = checkoutDetails['checkout-data'].inputFieldEmailValue;

            return email;

        },

        getCuracaoId: function () {
            if (this.customerData.custom_attributes.curacaocustid) {
                var curacaoid = this.customerData.custom_attributes.curacaocustid.value;
                var last4digits = curacaoid.slice(-4);

                return last4digits;
            }

            return null;
        },

        getCreditLimit: function () {
            return this.limit;
        },

        getDownPayment: function () {
            return this.downPayment;
        },

        /**
         * Registering curacao verification form modal.
         * Initiating modal only after the modal html dom is loaded.
         *
         * @param {HtmlElem} elem
         */
        initiateCuracaoVerifyModal: function (elem) {
            this.curacaoAccountVerifyModal = elem;
            $(elem).modal({
                title: $t('Verify your Curacao Account'),
                modalClass: 'curacao-verify-modal'
            });
        },

        /**
         * Open up curacao-verify-account-modal
         *
         * @param {this} model
         * @param {Event} event
         */
        openCuracaoVerifyForm: function (model, event) {
            event.preventDefault();
            $(this.curacaoAccountVerifyModal).modal('openModal');
        },

        /**
         * Verifying curacao account with given details.
         *
         * Fires when the user submit the curacao-verification-modal-form.
         *
         * @returns {Boolean}
         */
        verifyCuracaoAccount: function () {
            return false;
        }
    });
});
