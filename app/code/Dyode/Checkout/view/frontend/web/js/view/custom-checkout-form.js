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
    'mage/translate',
    'Magento_Ui/js/form/form',
    'Magento_Ui/js/model/messageList',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/model/quote',
    'Magento_Customer/js/model/customer',
    'Dyode_Checkout/js/model/curacao-service-provider',
    'Dyode_Checkout/js/data/curacao-data-provider',
    'Magento_Ui/js/modal/modal',
    'mage/calendar'
], function (
    $,
    ko,
    Url,
    $t,
    Component,
    messageList,
    checkoutData,
    quote,
    customer,
    curacaoServiceProvider,
    curacaoDataProvider
) {
    var curacaoPaymentInfo = window.checkoutConfig.curacaoPayment,
        customerInfo = window.checkoutConfig.customerData,
        isUserLinked = !!curacaoPaymentInfo.linked,

        /**
         * Helper Function
         *
         * Find last 4 digits of a string.
         * @param {String} string
         * @returns {String}
         */
        getLast4 = function (string) {
            if (string) {
                string = string.toString(); //make sure the value is string.

                if (string.length <= 4) {
                    return string;
                }

                return string.substring(string.length - 4);
            }

            return '';
        },

        curacaoLast4Digit = getLast4(curacaoPaymentInfo.curacaoId);

    return Component.extend({

        isLoggedIn: customer.isLoggedIn(),
        customerData: window.customerData,
        downPayment: curacaoPaymentInfo.downPayment,
        limit: curacaoPaymentInfo.limit,
        canApply: curacaoPaymentInfo.canApply,
        curacaoAccountVerifyModalTemplate: 'Dyode_Checkout/custom-form/account-verify-modal',
        curacaoAccountVerifyModal: null,
        smsIconUrl: curacaoPaymentInfo.mediaUrl + '/images/sms-icon.png',
        callIconUrl: curacaoPaymentInfo.mediaUrl + '/images/call-icon.png',
        personalInfoForm: 'pinfm',
        ssnInputFieldId: 'curacao-ssn-verify',
        dateOfBirthInpFieldId: 'curacao-date-of-birth',
        zipInputFieldId: 'curacao-zip-code',
        maidenNameInpFieldId: 'curacao-maiden-name',
        isUserLinked: ko.observable(isUserLinked),
        curacaoAccountIdInpValue: ko.observable(''),
        verificationCodeInpValue: ko.observable(''),
        ssnVerifyInpValue: ko.observable(''),
        dateOfBirthInpValue: ko.observable(''),
        zipCodeInpValue: ko.observable(''),
        maidenNameInpValue: ko.observable(''),
        curacaoIdLast4Digit: ko.observable(curacaoLast4Digit),
        curacaoUserCreditLimit: ko.observable(curacaoPaymentInfo.limit),
        curacaoUserDownPayment: curacaoDataProvider.downPayment,
        canShowDownPayment: ko.observable(true),

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super().observe({
                ApplyDiscount: ko.observable(true)
            });

            /**
             * If the curacao-credit checkox is checked/unchecked, then the update quote totals accordingly.
             */
            this.ApplyDiscount.subscribe(function (newValue) {
                if (newValue) {
                    curacaoServiceProvider.collectCuracaoTotals().done(function () {
                        var successMessage = $t('Curacao Credits applied successfully.');

                        curacaoDataProvider.hasCuracaoCreditApplied(true);
                        messageList.addSuccessMessage({
                            message: successMessage
                        });
                    });
                } else {
                    curacaoServiceProvider.removeCuracaoTotals().done(function () {
                        var successMessage = $t('Curacao Credit removed successfully.');

                        curacaoDataProvider.hasCuracaoCreditApplied(false);
                        messageList.addSuccessMessage({
                            message: successMessage
                        });
                    });
                }
            }, this);

            this.ssnVerifyInpValue.subscribe(this.applyFormFieldDependencies, this);
            this.dateOfBirthInpValue.subscribe(this.applyFormFieldDependencies, this);
            this.zipCodeInpValue.subscribe(this.applyFormFieldDependencies, this);
            this.maidenNameInpValue.subscribe(this.applyFormFieldDependencies, this);

            return this;
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

            //Inititialize date picker for dob input field.
            $('#curacao-date-of-birth').datepicker({
                dateFormat: 'yy-mm-dd',
                changeMonth: true,
                changeYear: true,
                yearRange: '1850:2020'
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
                    quote_id: quote.getQuoteId(),
                    ssn_last: this.ssnVerifyInpValue(),
                    zip_code: this.zipCodeInpValue(),
                    date_of_birth: this.dateOfBirthInpValue(),
                    maiden_name: this.maidenNameInpValue()
                };

                $(model.curacaoAccountVerifyModal).modal('closeModal');

                curacaoServiceProvider.scrutinizeCuracaoUser(userInfo).done(function () {
                    model.curacaoAccountIdInpValue('');

                    if (!curacaoServiceProvider.isResponseError()) {

                        //send collect-totals request with curacao credits.
                        curacaoServiceProvider.collectCuracaoTotals().done(function () {
                            var successMessage = $t('Curacao account is linked successfully to the email address') +
                                ': ' +
                                model.getUserEmail();

                            messageList.addSuccessMessage({
                                message: successMessage
                            });
                            model.isUserLinked(true);
                            model.processCuracaoLinkedScenario();
                        });

                    } else {
                        messageList.addErrorMessage({
                            message: curacaoServiceProvider.message()
                        });
                        model.isUserLinked(false);
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
        },

        /**
         * Process guest user turned to curacao linked customer.
         * We need to show down payment details in this case.
         */
        processCuracaoLinkedScenario: function () {
            curacaoDataProvider.hasCuracaoCreditApplied(true);

            if (curacaoServiceProvider.response() && curacaoServiceProvider.response().curacaoInfo) {
                var curacaoInfo = curacaoServiceProvider.response().curacaoInfo;

                this.curacaoUserCreditLimit(curacaoInfo.creditLimit);

                if (curacaoInfo.downPayment) {
                    this.curacaoUserDownPayment(curacaoInfo.downPayment);
                    this.canShowDownPayment(true);
                } else {
                    this.canShowDownPayment(false);
                }
            }
        },

        /**
         * Validate each field in the curacao modal form as per the user input.
         *
         * Condition 1: ssn field is there, then dob is the necessary field.
         * Condition 2: if maiden name provided, then either zip-code or dob should be filled.
         */
        applyFormFieldDependencies: function () {
            var ssnInputRef = '#' + this.ssnInputFieldId,
                dobInputRef = '#' + this.dateOfBirthInpFieldId,
                zipInputRef = '#' + this.zipInputFieldId,
                maidenInpRef = '#' + this.maidenNameInpFieldId,
                SSNInput = $(ssnInputRef),
                DOBInput = $(dobInputRef),
                ZipInput = $(zipInputRef),
                MaidenInput = $(maidenInpRef),
                hasSSN =  this.ssnVerifyInpValue() !== '',
                hasDOB = this.dateOfBirthInpValue() !== '',
                hasZIP = this.zipCodeInpValue() !== '',
                hasMaiden = this.maidenNameInpValue() !== '';

            //SSN Input
            if (!hasDOB && !hasZIP && !hasMaiden || hasDOB && !hasZIP && !hasMaiden || hasDOB && hasZIP && !hasMaiden) {
                SSNInput.addClass('required');
            } else {
                SSNInput.removeClass('required');
            }

            //DOB Input
            if (!hasSSN && !hasMaiden && hasZIP || !hasSSN && hasMaiden && hasZIP) {
                DOBInput.removeClass('required');
            } else {
                DOBInput.addClass('required');
            }

            //ZIP Input
            if (!hasDOB && (!hasSSN && !hasMaiden || !hasSSN && hasMaiden || hasSSN && hasMaiden)) {
                ZipInput.addClass('required');
            } else {
                ZipInput.removeClass('required');
            }

            //Maiden Name Input
            if (hasSSN) {
                MaidenInput.removeClass('required');
            } else {
                MaidenInput.addClass('required');
            }
        }
    });
});
