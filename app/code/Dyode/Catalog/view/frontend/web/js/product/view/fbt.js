/**
 * Dyode_curacao theme.
 *
 * Extending Magento_Catalog
 *
 * @package Dyode
 * @module  Dyode_curacao
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */
define([
    'jquery',
    'underscore',
    'Magento_Catalog/js/price-utils',
    'Magento_Customer/js/customer-data',
    'Magento_Catalog/product/view/validation',
    'catalogAddToCart',
], function ($, _, priceUtils, customerData) {

    $.widget('dyodeCatalog.fbt', {

        options: {
            checkBoxInputs: 'input[type=checkbox]',
            currencySymbol: '$',
            addOnCountElem: '.addon-count',
            addOnTotalElem: '.add-on-total',
            fbtTotalElem: '.fbt-total',
            fbtButton: '.add-bundle-btn',
            productInputElem: '#fbt-products-inp',
            formElem: '#fbt-form',
            addToCartButtonText: 'Add Bundle to Cart',
        },

        /**
         * Add event hooks: checkbox click, add-bundle-to-cart button click
         * Also register fbt ajax form submit via magento-validator.
         * @private
         */
        _create: function () {
            this.addEventHooks();
            this.initiateFormValidationSubmitHook();
        },

        /**
         * Register module events.
         */
        addEventHooks: function () {
            this.fbtCheckboxes().on('click', this.manageCheckBoxClick.bind(this));
            this.fbtCartButton().on('click', this.manageFbtButtonClick.bind(this));
        },

        /**
         * Enable form submit via ajax.
         */
        initiateFormValidationSubmitHook: function () {
            var self = this;

            this.form().validation({

                /**
                 * Uses catalogAddToCart widget as submit handler.
                 * This allow us to submit form via ajax.
                 * @param {Object} form
                 * @returns {Boolean}
                 */
                submitHandler: function (form) {
                    var jqForm = $(form).catalogAddToCart({
                        bindSubmit: false,
                        addToCartButtonTextDefault: self.options.addToCartButtonText,
                    });

                    jqForm.catalogAddToCart('submitForm', jqForm);

                    return false;
                }
            });
        },

        /**
         * Perform add-to-cart action on the button click.
         *
         * @param {Event} event
         * @returns {boolean}
         */
        manageFbtButtonClick: function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();

            if (!this.canDoCartSubmit()) {
                return false;
            }

            var addonProductIds = this.prepareFbtProductIdsToAddCart();

            this.updateProductInputField(_.map(addonProductIds, Number));
            this.form().submit();
            window.scrollTo(0, 0);
            return true;
        },

        /**
         * Checkbox click event handler
         * It will redo "mathematics" of product calculation.
         */
        manageCheckBoxClick: function () {
            var checkedInputs = this.fbtCheckboxes().filter(':checked');
            var addonProductIds = this.collectInputValues(checkedInputs);

            this.changeAddonCount(checkedInputs.length);
            this.changeAddonTotal(this.calculateAddonTotal(addonProductIds));
            this.changeFbtTotal(this.calculateFbtTotal(addonProductIds));
        },

        /**
         * Prepare product id array eligible for adding into the cart.
         * Products which are ticked and the current product are eligible.
         *
         * @returns {*|Array}
         */
        prepareFbtProductIdsToAddCart: function () {
            var checkedInputs = this.fbtCheckboxes().filter(':checked'),
                addonProductIds = this.collectInputValues(checkedInputs);

            addonProductIds.push(this.options.product.id);

            return addonProductIds;
        },

        /**
         * Update fbt-product-ids input field.
         *
         * @param {String} productIds - Expect json converted array of product ids.
         */
        updateProductInputField: function (productIds) {
            $(this.options.productInputElem).val(JSON.stringify(productIds));
        },

        /**
         * Checks whether add-to-cart action to be performed
         * If no additional products selected, then skip add-to-cart action.
         *
         * @returns {boolean}
         */
        canDoCartSubmit: function () {
            if (!this.options.product) {
                return false;
            }
            if (this.fbtCheckboxes().filter(':checked').length === 0) {
                return false;
            }
            return true;
        },

        /**
         * Update fbt total section with the "total" value passed.
         * @param {Float} total
         */
        changeFbtTotal: function (total) {
            $(this.element).find(this.options.fbtTotalElem).html(
                priceUtils.formatPrice(total)
            );
        },

        /**
         * Update add-on's total section with the "total" value passed.
         * @param {Float} total
         */
        changeAddonTotal: function (total) {
            $(this.element).find(this.options.addOnTotalElem).html(
                priceUtils.formatPrice(total)
            );
        },

        /**
         * Change add-ons count section.
         *
         * @param {Int} count
         */
        changeAddonCount: function (count) {
            $(this.element).find(this.options.addOnCountElem).html(count);
        },

        /**
         * Calculate add-ons total based on the ids passed.
         * @param {Array} productIds
         * @returns {Float} price
         */
        calculateAddonTotal: function (productIds) {
            var fbtProducts = this.options.fbtProducts,
                price = 0;

            $.each(productIds, function (index, productId) {
                if (fbtProducts && fbtProducts[productId]) {
                    price += fbtProducts[productId].price;
                }
            });

            return price;
        },

        /**
         * Calculate FBT total
         *
         * @param {Array} productIds
         * @returns {Float}
         */
        calculateFbtTotal: function (productIds) {
            return this.calculateAddonTotal(productIds) + this.options.product.price;
        },

        /**
         * Collect value property of input fields passed into an array.
         * @param {*|jQuery|HTMLElement} inputs
         * @returns {Array} values
         */
        collectInputValues: function (inputs) {
            var values = [];
            inputs.each(function (index, elem) {
                values.push(elem.value);
            });
            return values;
        },

        /**
         * FBT form to submit.
         *
         * @returns {*|jQuery|HTMLElement}
         */
        form: function () {
            return $(this.options.formElem);
        },

        /**
         * Add-to-cart button
         *
         * @returns {*|jQuery|HTMLElement}
         */
        fbtCartButton: function () {
            return $(this.element).find(this.options.fbtButton);
        },

        /**
         * FBT checkbox fields.
         *
         * @returns {*|jQuery|HTMLElement}
         */
        fbtCheckboxes: function () {
            return $(this.element).find(this.options.checkBoxInputs);
        },

        /**
         * Browser logger
         * @param {*} arg1
         * @param {*} arg2
         */
        log: function (arg1, arg2) {
            if (!arg2) {
                arg2 = 'dyode_catalog';
            }
            console.log(arg2, arg1);
        }
    });

    return $.dyodeCatalog.fbt;
});
