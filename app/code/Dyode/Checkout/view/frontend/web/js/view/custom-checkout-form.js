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
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Customer/js/model/customer',
    'Dyode_Checkout/js/model/curacao-service-provider',
    'Magento_Ui/js/modal/modal'
], function (
    $,
    ko,
    Url,
    storage,
    $t,
    Component,
    messageList,
    checkoutData,
    quote,
    urlBuilder,
    fullScreenLoader,
    getPaymentInformationAction,
    totals,
    customer,
    curacaoServiceProvider
) {
    var curacaoPaymentInfo = window.checkoutConfig.curacaoPayment,
        customerInfo = window.checkoutConfig.customerData;

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
        personalInfoForm: 'pinfm',
        curacaoAccountIdInpValue: ko.observable(''),
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
            return Url.build('dyode_checkout/curacao/verify');
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
            ).fail(function (response) {
                //   alert(response);
            }).always(function () {
                fullScreenLoader.stopLoader();
            });
        },

        /**
         * Collect user email address.
         *
         * If logged-in customer, then collect it from global data or else bring the data from checkout data.
         *
         * @returns {String}
         */
        getUserEmail: function () {
            var email = '';

            if (customerInfo.length > 0) {
                email = customerInfo.email;
            } else {
                email = checkoutData.getValidatedEmailValue();
            }

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
         * Verifying given curacao id
         *
         * If curacao id is valid, then open up the verification-modal
         *
         * @param {this} model
         * @param {Event} event
         */
        verifyCuracaoId: function (model, event) {
            event.preventDefault();

            if (this.validateCuracaoId(model)) {
                var payload = {
                    'email_address': this.getUserEmail(),
                    'curacao_account': this.curacaoAccountIdInpValue()
                };

                curacaoServiceProvider.verifyCuracaoId(payload).done(function () {
                    if (!curacaoServiceProvider.isResponseError()) {
                        $(model.curacaoAccountVerifyModal).modal('openModal');
                    } else {
                        model.curacaoAccountIdInpValue('');
                        messageList.addErrorMessage({
                            message: curacaoServiceProvider.message()
                        });
                    }
                });
            }
        },

        /**
         * Verifying curacao account with given details.
         *
         * Fires when the user submit the curacao-verification-modal-form.
         *
         */
        verifyCuracaoAccount: function (model, event) {
            event.preventDefault();

            if (this.validateCuracaoVerifyModalForm(model)) {
                var userInfo = {
                    ssn_last: this.ssnVerifyInpValue(),
                    zip_code: this.zipCodeInpValue(),
                    date_of_birth: this.dateOfBirthInpValue(),
                    maiden_name: this.maidenNameInpValue()
                };

                $(model.curacaoAccountVerifyModal).modal('closeModal');

                curacaoServiceProvider.scrutinizeCuracaoUser(userInfo).done(function () {
                    model.curacaoAccountIdInpValue('');

                    if (!curacaoServiceProvider.isResponseError()) {
                        var successMessage = $t('Curacao account is linked successfully to the email address') +
                            ': ' +
                            model.getUserEmail();

                        messageList.addSuccessMessage({
                            message: successMessage
                        });
                    } else {
                        messageList.addErrorMessage({
                            message: curacaoServiceProvider.message()
                        });
                    }
                });
            }
        },

        /**
         * Validate curacao id.
         *
         * Curacao id is an 8 or 7 digit number.
         *
         * @param   {Object}  model
         * @returns {Boolean}
         */
        validateCuracaoId: function (model) {
            if (model.curacaoAccountIdInpValue() === '') {
                messageList.addErrorMessage({
                    message: $t('Curacao account number cannot be empty.')
                });

                return false;
            }

            if (isNaN(model.curacaoAccountIdInpValue())) {
                messageList.addErrorMessage({
                    message: $t('Curacao account number should be a number.')
                });

                return false;
            }

            if (model.curacaoAccountIdInpValue().length !== 8 && model.curacaoAccountIdInpValue().length !== 7) {
                messageList.addErrorMessage({
                    message: $t('Curacao account number provided is invalid.')
                });

                return false;
            }

            return true;
        },

        /**
         * Validate curacao-verify-modal-form
         *
         * @param {Object} model
         * @returns {Boolean}
         */
        validateCuracaoVerifyModalForm: function (model) {
            var formId = '#' + model.personalInfoForm,
                personalForm = $(formId);

            personalForm.validate();

            return personalForm.valid();
        }
    });
});
