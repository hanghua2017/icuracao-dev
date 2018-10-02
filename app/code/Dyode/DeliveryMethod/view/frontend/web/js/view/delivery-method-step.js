/**
 * Dyode_DeliveryMethod Magento2 Module.
 *
 * Add a new checkout step in checkout
 *
 * @module    Dyode_DeliveryMethod
 * @copyright Copyright © Dyode
 */

'use strict';

/**
 * Product Delivery Options Step
 */
define([
        'jquery',
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/quote',
        'Magento_Catalog/js/price-utils',
        'mage/translate',
        'Magento_Checkout/js/model/full-screen-loader',
        'mage/url',
        'Magento_Ui/js/modal/modal'
    ],
    function (
        $,
        ko,
        Component,
        _,
        stepNavigator,
        quote,
        priceUtils,
        $t,
        fullScreenLoader,
        Url
    ) {

        var quoteItemData = window.checkoutConfig.quoteItemData;

        return Component.extend({
            defaults: {
                template: 'Dyode_DeliveryMethod/delivery-method',
                isVisible: ko.observable(true),
                storeList: ko.observableArray([])
            },
            stepCode: 'deliverySelection',
            stepTitle: 'Delivery Method',
            defaultStoreModalData: {
                zipCode: '',
                storeInfo: {
                    items: []
                }
            },

            /**
             * @inheritdoc
             */
            initialize: function () {
                this._super();

                // register delivery step
                stepNavigator.registerStep(
                    this.stepCode,
                    this.stepCode,
                    $t(this.stepTitle),
                    this.isVisible,
                    _.bind(this.navigate, this),
                    5
                );

                return this;
            },

            /**
             * @inheritdoc
             */
            navigate: function () {},

            /**
             * @inheritdoc
             */
            navigateToNextStep: function () {
                stepNavigator.next();
            },

            /**
             * Preparing product array.
             * Entire delivery option section is creating using this array.
             * @returns {Array} products - KO Observable array
             */
            productList: function () {
                var self = this,
                    products = ko.observableArray([]);

                $(quoteItemData).each(function (index, quoteItem) {
                    products.push({

                        //quote related data
                        quoteId: quoteItem.quote_id,
                        quoteItemId: quoteItem.item_id,
                        quoteQty: quoteItem.qty.toString(),

                        //product related data
                        productId: quoteItem.product_id,
                        productName: quoteItem.product.name,
                        productPrice: priceUtils.formatPrice(quoteItem.product.price).toString(),
                        productImageUrl: quoteItem.thumbnail,

                        //forms related data
                        deliveryRadioInputName: 'delivery_option_' + quoteItem.item_id,
                        deliveryRadioInputShipId: 'delivery_option_ship_' + quoteItem.item_id,
                        deliveryRadioInputStoreId: 'delivery_option_store_' + quoteItem.item_id,
                        zipFormId: 'delivery_option_store_form_' + quoteItem.item_id,
                        zipInp: 'delivery_option_store_zip_' + quoteItem.item_id,
                        storePickupModalId: 'store_pickup_modal_' + quoteItem.item_id,
                        storePickupModalFormInp: 'store_pickup_modal_' + quoteItem.item_id + '_form_inp',
                        storeDisplaySectionId: 'selected_store_section_' + quoteItem.item_id,
                        deliveryRadioShip: ko.observable('ship'),
                        deliveryRadioStore: ko.observable('store'),
                        deliveryRadioShipChecked: ko.observable(true),
                        deliveryRadioStoreChecked: ko.observable(false),
                        showZipForm: ko.observable(false),
                        showZipFormError: ko.observable(false),
                        showZipInputModalForm: ko.observable(false),
                        showStoreSelectedSection: ko.observable(false),
                        zipInpValue: ko.observable(''),
                        currentStoreModal: ko.observable(self.defaultStoreModalData),
                        currentSelectedStore: ko.observable({}),
                        storeResponseError: ko.observable(false),
                        storeModals: [],
                        storeList: self.storeList,

                        //function associated
                        collectStores: self.selectLocation,
                        selectStore: self.selectProductStore,
                        transformStoreModalHeading: self.transformStoreModalHeading
                    });
                });

                return products;
            },

            /**
             * If delivery option (radio button) against a button is triggered.
             * Toggling store form here.
             * @param {Object} model
             * @param {Event} event
             */
            onDeliveryOptionChange: function (model, event) {
                var showZipForm = true,
                    showStoreSelected = true;

                if (event.target.value == 'ship') {
                    model.deliveryRadioStoreChecked(false);
                    showZipForm = false;
                    showStoreSelected = false;
                } else {
                    model.deliveryRadioShipChecked(false);
                }

                if (model.currentSelectedStore().zipCode) {
                    showZipForm = false;
                } else {
                    showStoreSelected = false;
                }

                model.showZipForm(showZipForm);
                model.showStoreSelectedSection(showStoreSelected);
            },

            /**
             * This will initiate delivery option store modals after the knockout
             * rendering is completed.
             * @param {HTMLElement} elem
             */
            initiatePickupModal: function (elem) {

                //Here "this" in productList-foreach context
                var quoteItemId = $(elem).data('parent');

                if (quoteItemId) {
                    $(elem).modal({
                        title: $t('Showing free in store pickup for the Zip code'),
                        modalClass: 'store-pickup-modal'
                    });

                    this.storeModals.push({
                        quoteItemId: quoteItemId,
                        modalElement: elem.id,
                        zipCode: '',
                        storeInfo: {
                            quoteItemId: quoteItemId,
                            zipCode: '',
                            items: []
                        }
                    });
                }

            },

            /**
             * This will provide a provision to pickup a store in case user selected
             * the delivery option: store pickup.
             * @param {Object} model
             * @param {Event} event
             */
            selectStore: function (model, event) {
                event.preventDefault();

                /**
                 * @todo need to do validation
                 */
                if (model.zipInpValue() == '') {
                    model.showZipFormError(true);

                    return;
                }

                model.showZipFormError(false);

                //check whether a modal with the zip code entered exists;
                var existingModal = _.findWhere(model.storeModals, {
                    modalElement: model.storePickupModalId,
                    zipCode: model.zipInpValue()
                });

                if (existingModal) {
                    $('#' + model.storePickupModalId).modal('openModal');
                } else {
                    model.collectStores(model);
                }
            },

            /**
             * This will be performed when user pick a store from the store list provided.
             * Selected store will be saved for future use.
             * @param {Object} model
             * @param {Event} event
             */
            selectProductStore: function (model, event) {

                //keep selected store data in an array so that we can use it in next steps.
                var form = $(event.target).closest('form'),
                    storeData = {},
                    selectedStoreInfo = {};

                _.each(form.serializeArray(), function (formData) {
                    storeData[formData.name] = formData.value;
                });

                _.each(this.storeModals, function (storeModal) {
                    _.each(storeModal.storeInfo.items, function (storeItem) {
                        if (storeItem.id == storeData.storeId) {
                            selectedStoreInfo.title = storeItem.name;
                            selectedStoreInfo.image = storeItem.image;
                            selectedStoreInfo.streetCity = storeItem.address.street + storeItem.address.city;
                            selectedStoreInfo.zipCode = storeItem.address.zip;
                        }

                    });
                });

                this.storeList.push(storeData);

                //display selected store dom and change button;hide zip form.
                this.showZipForm(false);
                this.showStoreSelectedSection(true);
                this.showZipInputModalForm(false);
                this.storeResponseError(false);
                this.currentSelectedStore(selectedStoreInfo);

                //Finally, closing the modal.
                $('#' + this.storePickupModalId).modal('closeModal');
            },

            /**
             * This will be triggered when user needs to change the selected store.
             * @param {Object} model - Product loop context
             * @param {Event} event
             */
            changeStore: function (model, event) {
                //here "this" has component context
                this.selectStore(model, event);
            },

            /**
             * Update store modal heading with stores count
             * @returns {String}
             */
            transformStoreModalHeading: function () {
                var storeCount = this.currentStoreModal().storeInfo.items.length;

                return 'Available Stores <span class=\'store-count\'>(' + storeCount + ')</span>';
            },

            /**
             * This will be fired when the user need to change the zip code and search stores form the
             * store modal.
             * @param {Object} model
             */
            changeZipCode: function (model) {
                model.showZipInputModalForm(true);
            },

            /**
             * Collect stores from backend side and show it in the storeModal
             * @param {Object} viewModel
             */
            selectLocation: function (viewModel) {
                var existingModal = _.findWhere(viewModel.storeModals, {
                    modalElement: viewModel.storePickupModalId
                });

                if (!existingModal) {
                    return false;
                }

                if (viewModel.zipInpValue() && viewModel.productId) {

                    fullScreenLoader.startLoader();

                    $.ajax({
                        url: Url.build('/storeloc/storelocator/getstores'),
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            zipcode: viewModel.zipInpValue(),
                            pid: viewModel.productId
                        },

                        /**
                         * Urekkaa.. got response
                         * @param {JSON} response
                         */
                        success: function (response) {
                            fullScreenLoader.stopLoader();

                            if (response.error) {
                                viewModel.storeResponseError(true);
                                $('#' + viewModel.storePickupModalId).modal('openModal');

                                return;
                            }

                            viewModel.storeResponseError(false);

                            //update and replace the modal into storeModals
                            existingModal.zipCode = viewModel.zipInpValue();
                            existingModal.storeInfo = {
                                quoteItemId: viewModel.quoteItemId,
                                zipCode: viewModel.zipInpValue(),
                                items: response
                            };
                            _.extend(
                                _.findWhere(viewModel.storeModals, {
                                modalElement: viewModel.storePickupModalId
                            }),
                                existingModal
                            );

                            //fill current modal with store data and open up.
                            viewModel.currentStoreModal(existingModal);
                            $('#' + viewModel.storePickupModalId).modal('openModal');
                        },

                        /**
                         * Some bad thing happend in Ajax request
                         */
                        error: function () {
                            viewModel.storeResponseError(true);
                            $('#' + viewModel.storePickupModalId).modal('openModal');
                        },

                        /**
                         * Ajax request complete
                         */
                        complete: function () {
                            fullScreenLoader.stopLoader();
                        }
                    });
                }
            }

        });
    }
);
