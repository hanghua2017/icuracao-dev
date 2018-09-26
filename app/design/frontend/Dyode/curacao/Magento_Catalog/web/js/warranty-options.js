/**
 * Dyode_curacao theme.
 *
 * @package Dyode
 * @module  Dyode_curacao
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

/**
 * This defines a new jquery widget dyode.warranty
 * It also initialize warranty-details-modal and warranty-addtocart-prompt-modal.
 *
 *  Following actions are handled by dyode.warranty widget
 *
 *  1. Open warranty-details-modal with warranty details population when warranty option link is clicked.
 *  2. Shows warranty-prompt-modal with warranty options when add-to-cart button clicked.
 *  3. Allow only one warranty option get selected at a time.
 */
define([
    'jquery',
    'mage/translate',
    'Magento_Ui/js/modal/modal',
], function ($, $t) {
    'use strict';

    /**
     ////////////////////////////////////////////////////////////////////
     //////                WARRANTY DETAILS MODAL                  //////
     ////////////////////////////////////////////////////////////////////
     */


    /**
     * Warranty Details Modal initializer
     */
    function initializeWarrantyDetailsModal() {
        var warrantyModal = $('#warrantyModal');

        /**
         * Handles warranty details modal buttons action.
         *
         * For the "Add" button, the corresponding checkbox will be checked.
         * For the "Skip" button, just close the modal without doing any action.
         *
         * @param {Event} event
         * @returns {boolean}
         */
        const warrantyDetailsButtonClick = function (event) {
            event.preventDefault();

            var button = false;

            //we need to know the button clicked on modal is the "Add" button
            if ($(event.target).prop('tagName').toLowerCase() !== 'button') {
                button = $(event.target).closest('button');
            }

            //if the button is add button, then select the warranty option checkbox
            if (button !== false && button.hasClass('add-warranty-btn')) {
                var checkboxInpId = '#' + warrantyModal.find('input[name="warranty"]').val();

                if ($(checkboxInpId).length === 1) {
                    $(checkboxInpId).trigger('click');
                }
            }

            warrantyModal.modal('closeModal');
            return false;
        };

        /**
         * Initiate Warranty Modal on the dom load.
         */
        warrantyModal.modal({
            title: $t('Add to your order'),
            modalClass: 'warranty-details-popup',
            buttons: [
                {
                    text: 'Add',
                    class: 'add-warranty-btn',
                    attr: {},
                    click: warrantyDetailsButtonClick,
                },
                {
                    text: 'Skip',
                    class: 'skip-warranty-btn',
                    attr: {},
                    click: warrantyDetailsButtonClick,
                },
            ],
        });
    };


    /**
     ////////////////////////////////////////////////////////////////////
     //////                WARRANTY ADD-TO-CART MODAL              //////
     ////////////////////////////////////////////////////////////////////
     */

    /**
     * Initialize warranty addtocart warranty popup
     */
    function initializeWarrantyAddToCartModal() {
        var addToCartModal = $('#addtocartModal'),
            addToCartButton = $('#product-addtocart-button'),
            redirectToCartHiddenLink = $('#warranty-go-to-cart-link')[0];

        /**
         * Warranty modal Add, Skip button action.
         * Both buttons will perform addtocart action and then redirect to the cart page.
         */
        const addToCartButtonClick = function (event) {
            addToCartButton.trigger('submit');
            redirectToCartHiddenLink.click();
            addToCartModal.modal('closeModal')
            return true;
        };

        /**
         * Warranty modal initialization
         */
        addToCartModal.modal({
            title: $t("Add to your order"),
            modalClass: 'warranty-addtocart-popup',
            buttons: [
                {
                    text: 'Add',
                    class: 'add-warranty-btn',
                    attr: {},
                    click: addToCartButtonClick,
                },
                {
                    text: 'Skip',
                    class: 'skip-warranty-btn',
                    attr: {},
                    click: addToCartButtonClick,
                },
            ],
        });
    };

    /**
     ////////////////////////////////////////////////////////////////////
     //////                HTML DOM READY                          //////
     ////////////////////////////////////////////////////////////////////
     */

    /**
     * Executes when DOM is ready.
     * Initialize warranty modals
     */
    $(function () {
        initializeWarrantyDetailsModal();
        initializeWarrantyAddToCartModal();
    });


    /**
     ////////////////////////////////////////////////////////////////////
     //////                DYODE WARRANTY WIDGET                   //////
     ////////////////////////////////////////////////////////////////////
     */

    /**
     * Jquery widget which manages warrany options.
     */
    $.widget('dyode.warranty', {

        options: {
            checkBoxInputs: 'input[type=checkbox]',
            warrantyLinks: '.warranty-link',
            warrantyModal: '#warrantyModal',
            warrantyModalPrice: '.warranty-price',
            warrantyModalDescription: '.warranty-description',
            warrantyInfo: [],
            warrantyModalCmsSection: '.top-section',
            warrantyModalInput: 'input[name="warranty"]',
            addToCartButton: '#product-addtocart-button',
            warrantyAddToCartModal: '#addtocartModal',
        },

        /**
         * Register warranty option events.
         * @inheritDoc
         */
        _create: function () {
            this.addEventHooks();
        },

        /**
         * Bind warranty link click and warranty checkbox click
         */
        addEventHooks: function () {
            $(this.element).find(this.options.warrantyLinks).click(this.warrantyLinkClickHandler.bind(this));
            $(this.element).find(this.options.checkBoxInputs).click(this.checkboxClickHandler.bind(this));
            $(this.options.addToCartButton).click(this.addToCartButtonClickHandler.bind(this));
            $(this.options.warrantyAddToCartModal)
                .find(this.options.checkBoxInputs)
                .click(this.checkboxModalClickHandler.bind(this));
        },

        /**
         * Handle warranty link click
         * A modal will be shown with the details of the warranty.
         * @param {Event} event
         * @returns {boolean}
         */
        warrantyLinkClickHandler: function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();

            this.updateModalContent($(event.target).data('warranty-id'))

            $(this.options.warrantyModal).modal('openModal');
            return true;
        },

        /**
         * Handle warranty checkbox click.
         * Allow only one checkbox ticked at a time.
         * @param {Event} event
         */
        checkboxClickHandler: function (event) {
            if (event.target.checked) {
                $(this.element).find(this.options.checkBoxInputs).not(event.target).attr('checked', false);
            }
        },

        /**
         * Handle warranty addtocart modal checkbox click.
         * Allow only one checkbox ticked at a time.
         * @param {Event} event
         */
        checkboxModalClickHandler: function (event) {
            var targetCheckbox = event.target,
                chekboxName = $(targetCheckbox).attr('name'),
                warrantyCheckbox = 'input[name="' + chekboxName + '"]';

            if (targetCheckbox.checked) {
                $(this.options.warrantyAddToCartModal)
                    .find(this.options.checkBoxInputs)
                    .not(targetCheckbox)
                    .attr('checked', false);

                $(this.element).find(warrantyCheckbox).trigger('click');
            }
        },

        /**
         * Add to cart button click customization
         * Show warranty popups if warranty does exist;
         */
        addToCartButtonClickHandler: function (event) {
            event.preventDefault();

            var warrantyInputs = $(this.element).find(this.options.checkBoxInputs);

            //skip warranty popup if any of the warranty options selected.
            if (warrantyInputs.length === 0 || warrantyInputs.is(':checked')) {
                $(this.options.addToCartButton).trigger('submit');
                return true;
            }

            $(this.options.warrantyAddToCartModal).modal('openModal');

            return true;
        },

        /**
         * Update warrany details modal content.
         * @param {String} warrantyId - Warranty product id
         */
        updateModalContent: function (warrantyId) {
            if (warrantyId) {
                var warranty = this.options.warrantyInfo[warrantyId];

                if (warranty) {
                    var warrantyModal = $(this.options.warrantyModal),
                        checkboxInpId = 'warranty-option-' + warranty.id;

                    warrantyModal.find(this.options.warrantyModalInput).val(checkboxInpId);
                    warrantyModal.find(this.options.warrantyModalCmsSection).html(warranty.cmsBlockHtml);
                    warrantyModal.find(this.options.warrantyModalPrice).html(warranty.price);
                    warrantyModal.find(this.options.warrantyModalDescription).html(warranty.description);
                }
            }
        },
    });

    return $.dyode.warranty;
});