/**
 * Dyode_curacao theme.
 *
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
    'Magento_Catalog/product/view/validation',
    'mage/mage'
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
         * @returns {Boolean}
         */
        var warrantyDetailsButtonClick = function (event) {
            event.preventDefault();

            var button = $(event.target);

            //we need to know the button clicked on modal is the "Add" button
            if ($(event.target).prop('tagName').toLowerCase() !== 'button') {
                button = $(event.target).closest('button');
            }

            //if the button is add button, then select the warranty option checkbox
            if (button.length === 1 && button.hasClass('add-warranty-btn')) {
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
                    click: warrantyDetailsButtonClick
                },
                {
                    text: 'Skip',
                    class: 'skip-warranty-btn',
                    attr: {},
                    click: warrantyDetailsButtonClick
                }
            ]
        });
    }


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
            needToRedirectInput = $('#addtocart_modal_need_to_redirect'),
            messageBlock = addToCartModal.find('.message'),
            modalCheckboxes = addToCartModal.find('input[type="checkbox"]');

        /**
         * Warranty modal Add, Skip button action.
         * Both buttons will perform addtocart action and then redirect to the cart page.
         */
        var addToCartButtonClick = function (event) {
            var button = $(event.target);

            if ($(event.target).prop('tagName').toLowerCase() !== 'button') {
                button = $(event.target).closest('button');
            }

            if (button.hasClass('add-warranty-btn')) {
                if (!modalCheckboxes.is(':checked')) {
                    messageBlock.html(
                        '<div class="error">' + $t('Please select any of the options available') + '</div>'
                    );

                    return false;
                }
            }

            messageBlock.html('');
            needToRedirectInput.val('1');
            addToCartButton.trigger('submit');
            addToCartModal.modal('closeModal');

            return true;
        };

        /**
         * Warranty modal initialization
         */
        addToCartModal.modal({
            title: $t('Add to your order'),
            modalClass: 'warranty-addtocart-popup',
            buttons: [
                {
                    text: 'Add',
                    class: 'add-warranty-btn',
                    attr: {},
                    click: addToCartButtonClick
                },
                {
                    text: 'Skip',
                    class: 'skip-warranty-btn',
                    attr: {},
                    click: addToCartButtonClick
                }
            ]
        });
    }

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
            warrantyAddToCartModal: '#addtocartModal'
        },

        /**
         * Register warranty option events.
         * @inheritdoc
         */
        _create: function () {
            this.addEventHooks();
        },

        /**
         * Bind warranty link click and warranty checkbox click
         */
        addEventHooks: function () {
            var warrantyLinks = $(this.element).find(this.options.warrantyLinks),
                warrantyOptions = $(this.element).find(this.options.checkBoxInputs),
                addToCartButton = $(this.options.addToCartButton),
                addToCartModalCheckboxes = $(this.options.warrantyAddToCartModal).find(this.options.checkBoxInputs);

            warrantyLinks.click(this.warrantyLinkClickHandler.bind(this));
            warrantyOptions.click(this.checkboxClickHandler.bind(this));
            addToCartButton.click(this.addToCartButtonClickHandler.bind(this));
            addToCartModalCheckboxes.click(this.checkboxModalClickHandler.bind(this));
            $(document).on('ajax:addToCart', this.ajaxAddToCartSuccess.bind(this));
        },

        /**
         * Handle warranty link click
         * A modal will be shown with the details of the warranty.
         * @param {Event} event
         * @returns {Boolean}
         */
        warrantyLinkClickHandler: function (event) {
            event.preventDefault();
            event.stopImmediatePropagation();

            this.updateModalContent($(event.target).data('warranty-id'));

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
                checkboxName = $(targetCheckbox).attr('name'),
                warrantyCheckbox = 'input[name="' + checkboxName + '"]';

            if (targetCheckbox.checked) {
                $(this.options.warrantyAddToCartModal)
                    .find(this.options.checkBoxInputs)
                    .not(targetCheckbox)
                    .attr('checked', false);

                var warrantyOption = $(this.element).find(warrantyCheckbox);

                warrantyOption.attr('checked', true);
                $(this.element).find(this.options.checkBoxInputs).not(warrantyOption).attr('checked', false);
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

            var addToCartModal = $(this.options.warrantyAddToCartModal),
                addToCartModalCheckboxes = addToCartModal.find(this.options.checkBoxInputs),
                messageBlock = addToCartModal.find('.message');

            addToCartModalCheckboxes.attr('checked', false);
            messageBlock.html('');
            addToCartModal.modal('openModal');

            return true;
        },

        /**
         * Listen to ajax add-to-cart success action.
         * If this action is fired, then that means the validations are completed and the produt is added to the
         * cart. So we are checking here whether we want to redirect the user to the cart page or not. We want
         * to redirect the user, if the product is added via warranty-addtocart-modal.
         */
        ajaxAddToCartSuccess: function () {
            var needToRedirectInput = $('#addtocart_modal_need_to_redirect'),
                goToCartHiddenLink = $('#warranty-go-to-cart-link')[0];

            if (needToRedirectInput.val() == 1) {
                needToRedirectInput.val('0');
                goToCartHiddenLink.click();
            }
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
        }
    });

    return $.dyode.warranty;
});
