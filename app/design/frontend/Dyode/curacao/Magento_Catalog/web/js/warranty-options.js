/**
 * Dyode_curacao theme.
 *
 * @package Dyode
 * @module  Dyode_curacao
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */
define([
    'jquery',
    'mage/translate'
], function ($, $t) {
    'use strict';

    var warrantyModal = $('#warrantyModal');

    $(function () {

        /**
         * Handles warranty modal buttons action.
         *
         * For the "Add" button, the corresponding checkbox will be checked.
         * For the "Skip" button, just close the modal without doing any action.
         *
         * @param {Event} event
         * @returns {boolean}
         */
        const buttonClick = function (event) {
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
                    click: buttonClick,
                },
                {
                    text: 'Skip',
                    class: 'skip-warranty-btn',
                    attr: {},
                    click: buttonClick,
                },
            ],
        });
    });

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
        },

        /**
         * Register warranty option events.
         * @inheritDoc
         */
        _create: function () {
            this.addEventHooks();
        },

        /**
         * Bind warrantly link click and warrany checkbox click
         */
        addEventHooks: function () {
            $(this.element).find(this.options.warrantyLinks).click(this.warrantyLinkClickHandler.bind(this));
            $(this.element).find(this.options.checkBoxInputs).click(this.checkboxClickHandler.bind(this));
        },

        /**
         * Handle warranty link click
         *
         * A modal will be shown with the details of the warranty.
         *
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
         *
         * Allow only one checkbox ticked at a time.
         * @param {Event} event
         */
        checkboxClickHandler: function (event) {
            if (event.target.checked) {
                $(this.element).find(this.options.checkBoxInputs).not(event.target).attr('checked', false);
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
        },
    });

    return $.dyode.warranty;
});