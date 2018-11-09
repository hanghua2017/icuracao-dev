/**
 * Dyode_curacao theme.
 *
 * @module  Dyode_curacao
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

/**
 * Deals with store availability functionality in the PDP
 *
 * This includes:-
 *
 * - Register necessary DOM events.
 * - Initialize store list modal.
 * - Sending store collection ajax request for each zipcode checking and showing the modal with stores list.
 */
define([
    'jquery',
    'mage/translate',
    'mage/template',
    'mage/url',
    'text!Magento_Catalog/templates/modal/store-list.html',
    'jquery/validate',
    'Magento_Ui/js/modal/modal'
], function ($, $t, mageTemplate, Url, storeListTemplate) {

    $.widget('dyode.storeAvailability', {

        options: {
            checkStoreAvailabilitySection: '#store-availability-check',
            storeAvailabilityFormSection: '#store-availability-form',
            storeAvailabilityFormCancel: '#store-avail-form-cancel',
            storeAvailabilityZipField: '#store-availability-zip-field',
            storeAvailabilityFormSubmit: '#store-availability-form .submit',
            storeAvailabilityForm: '#product_addtocart_form',
            storeAvailabilityModal: '#store-availability-modal',
            storeAvailabilityModalContent: '#store-availability-modal .message',
            storeAvailabilityMessageSection: '#store-availability .messages'
        },
        isRequestSuccess: false,
        errorMessage: '',
        storesInfo: [],

        /**
         * @inheritdoc
         */
        _create: function () {
            this.registerEvents();
            this.initializeModal();
        },

        /**
         * Registering DOM events.
         */
        registerEvents: function () {
            $(this.options.checkStoreAvailabilitySection).click(this.storeAvailabilityClickHandler.bind(this));
            $(this.options.storeAvailabilityFormCancel).click(this.storeAvailabilityFormCancelHandler.bind(this));
            $(this.options.storeAvailabilityFormSubmit).click(this.storeAvailabilityFormSubmitHandler.bind(this));
        },

        /**
         * Initialize stores listing modal.
         */
        initializeModal: function () {
            $(this.options.storeAvailabilityModal).modal({
                title: $t('Showing available stores'),
                modalClass: 'store-availability-modal'
            });
        },

        /**
         * Handle "check store availability" button click.
         *
         * Open up zip-code checking form in order to see the stores list where the product is available.
         *
         * @param {Event} event
         * @returns {dyode.storeAvailability}
         */
        storeAvailabilityClickHandler: function (event) {
            event.preventDefault();

            $(this.options.checkStoreAvailabilitySection).hide();
            $(this.options.storeAvailabilityMessageSection).html('');
            $(this.options.storeAvailabilityZipField).val(this.options.customer.zip);
            $(this.options.storeAvailabilityFormSection).show();

            return this;
        },

        /**
         * Hide zip-code checking form and put back "check store availability" button back.
         *
         * @param {Event} event
         * @returns {dyode.storeAvailability}
         */
        storeAvailabilityFormCancelHandler: function (event) {
            event.preventDefault();

            $(this.options.storeAvailabilityFormSection).hide();
            $(this.options.checkStoreAvailabilitySection).show();

            return this;
        },

        /**
         * Validate zip-code entered and then send an ajax request to collect stores corresponds to the zip-code.
         *
         * @param {Event} event
         */
        storeAvailabilityFormSubmitHandler: function (event) {
            event.preventDefault();

            var self = this,
                form = $(this.options.storeAvailabilityForm),
                zipCode = $(this.options.storeAvailabilityZipField).val();

            //validate only the zip-code field;
            form.validate().element(this.options.storeAvailabilityZipField);

            if (form.valid()) {

                this.collectStores({
                    zip_code: zipCode,
                    product_id: this.options.product.id
                }).done(function () {
                    if (self.isRequestSuccess) {
                        self.populateStoreList();
                        self.openStoreListModal();
                    } else {
                        self.throwErrorMessage();
                    }
                });
            }
        },

        /**
         * Populate modal content section using a JS template.
         */
        populateStoreList: function () {
            var modalHtml = mageTemplate(storeListTemplate, this.storesInfo);

            $(this.options.storeAvailabilityModalContent).html(modalHtml);
        },

        /**
         * Open stores listing modal
         */
        openStoreListModal: function () {
            $(this.options.storeAvailabilityModal).modal('openModal');
        },

        /**
         * Show error message in case something went wrong with the stores modal showing process.
         */
        throwErrorMessage: function () {
            $(this.options.storeAvailabilityMessageSection).html(this.errorMessage);
        },

        /**
         * Ajax request to collect stores list.
         *
         * @param {Json} userInfo
         * @returns {*}
         */
        collectStores: function (userInfo) {
            var self = this,
                url = Url.build('dyode_catalog/product_stores/listbyzip');

            userInfo.isAjax = true;
            $('body').trigger('processStart');

            return $.ajax({
                url: url,
                type: 'POST',
                dataType: 'json',
                data: userInfo,

                /**
                 * Success
                 * @param {JSON} result
                 */
                success: function (result) {
                    $('body').trigger('processStop');

                    if (result.type === 'error') {
                        self.isRequestSuccess = false;
                        self.errorMessage = result.message;
                        self.storesInfo = [];
                    } else {
                        self.isRequestSuccess = true;
                        self.errorMessage = '';
                        self.storesInfo = result.data;
                    }
                },

                /**
                 * Some bad thing happend in Ajax request
                 */
                error: function () {
                    $('body').trigger('processStop');
                    self.isRequestSuccess = false;
                    self.errorMessage = $t('Something went wrong. Please try later');
                    self.storesInfo = [];
                },

                /**
                 * Ajax request complete
                 */
                complete: function () {
                    $('body').trigger('processStop');
                }
            });
        }
    });

    return $.dyode.storeAvailability;
});
