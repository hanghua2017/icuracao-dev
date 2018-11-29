/**
 * Dyode_CheckoutDeliveryMethod Magento2 Module.
 *
 * Add a new checkout step in checkout
 *
 * @module    Dyode_CheckoutDeliveryMethod
 * @copyright Copyright © Dyode
 * @author Rajeev K Tomy <rajeev.ktomy@dyode.com>
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
        '../data/delivery-data-provider',
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
        Url,
        deliveryDataProvider
    ) {

        'use strict';

        /**
         * Prepare delivery option radio button's checked attribute value.
         *
         * @return {Object} checked
         */
        var getRadioCheckedStatusInfo = function (quoteItemData) {
                var checked = {
                    shipToHome: false,
                    storePickup: false
                };

                _.each(quoteItemData, function (quoteItem) {
                    var deliveryInfo = _.findWhere(deliveryDataProvider.getDeliveryData(), {
                        quoteItemId: parseInt(quoteItem.item_id)
                    });

                    if (!deliveryInfo) {
                        return checked;
                    }

                    if (deliveryInfo.deliveryType == 'ship_to_home') {
                        checked.shipToHome = 'ship_to_home';
                    } else {
                        checked.storePickup = 'store_pickup';
                    }
                });

                return checked;
            },

            quoteItemData = window.checkoutConfig.quoteItemData,
            radioDefaultCheckedInfo = getRadioCheckedStatusInfo(quoteItemData);

        /**
         * Delivery Step Component
         *
         * @extends uiComponent
         */
        return Component.extend({
            defaults: {
                template: 'Dyode_CheckoutDeliveryMethod/delivery-method',
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
                    $t('Delivery Method'),
                    this.isVisible,
                    _.bind(this.navigate, this),
                    5
                );

                return this;
            },

            /**
             * @inheritdoc
             */
            navigate: function () {
            },

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

                _.each(quoteItemData, function (quoteItem) {
                    var showPickupOption = true,
                        pdtFreight = quoteItem.product.freight,
                        pdtShipRate = quoteItem.product.shprate,
                        productIsFreight = !!(pdtFreight && pdtFreight === '1'),
                        productIsDomestic = !!(pdtShipRate && pdtShipRate.toLowerCase() === 'domestic');

                    if (quoteItem.product_type === 'virtual') {
                        return false;
                    }

                    //if product is freight or domestic, then store pickup option should not show.
                    if (productIsFreight || productIsDomestic) {
                        showPickupOption = false;
                    }

                    products.push({

                        //quote related data
                        quoteId: quoteItem.quote_id,
                        quoteItemId: quoteItem.item_id,
                        quoteQty: quoteItem.qty.toString(),

                        //product related data
                        productId: quoteItem.product_id,
                        productName: self.htmlDecode(quoteItem.product.name),
                        productPrice: priceUtils.formatPrice(quoteItem.price, quote.getPriceFormat()),
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
                        deliveryRadioShip: ko.observable('ship_to_home'),
                        deliveryRadioStore: ko.observable('store_pickup'),
                        deliveryRadioShipChecked: ko.observable(radioDefaultCheckedInfo.shipToHome),
                        deliveryRadioStoreChecked: ko.observable(radioDefaultCheckedInfo.storePickup),
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
                        showPickupOption: showPickupOption,

                        //function associated
                        collectStores: self.selectLocation,
                        selectStore: self.selectProductStore,
                        transformStoreModalHeading: self.transformStoreModalHeading,
                        updateDeliveryInfo: self.updateDeliveryInfo
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
                    showStoreSelected = true,
                    radioInputValue = event.target.value;

                if (radioInputValue == 'ship_to_home') {
                    model.deliveryRadioStoreChecked(false);
                    showZipForm = false;
                    showStoreSelected = false;
                } else {
                    model.deliveryRadioShipChecked(false);

                    if (model.currentSelectedStore().zipCode) {
                        showZipForm = false;
                    } else {
                        showStoreSelected = false;
                    }
                }

                model.showZipForm(showZipForm);
                model.showStoreSelectedSection(showStoreSelected);

                this.updateDeliveryInfo(model.quoteItemId, {
                    deliveryType: radioInputValue
                });
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
                            selectedStoreInfo.street = storeItem.address.street;
                            selectedStoreInfo.cityAbbrZip = storeItem.address.city + ', ' + storeItem.address.region_code + ' ' + storeItem.address.zip;
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

                /**
                 * Here "this" context is: quoteItemList::foreach
                 * Updating delivery info with store picked.
                 */
                this.updateDeliveryInfo(this.quoteItemId, {
                    storeId: parseInt(model.id)
                });
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

            htmlDecode: function (htmlStr) {
                var parser = new DOMParser;

                htmlStr = htmlStr.replace('&Reg;', '&reg;');
                var dom = parser.parseFromString(htmlStr,
                    'text/html');

                return dom.body.textContent;
            },

            /**
             * Update delivery information based on the quote Item
             *
             * @param {String|Integer} quoteItemId
             * @param {Object} updateDeliveryInfo
             */
            updateDeliveryInfo: function (quoteItemId, updateDeliveryInfo) {

                //prepare new data against the quoteItem provided
                var deliveryData = deliveryDataProvider.getDeliveryData(),
                    deliveryInfo = _.extend(
                        _.findWhere(deliveryData, {
                            quoteItemId: parseInt(quoteItemId)
                        }),
                        updateDeliveryInfo
                    );

                //update delivery option observable array.
                if (deliveryInfo) {
                    var rejected = _.reject(deliveryData, function (quoteItem) {
                            return quoteItem.quoteItemId == deliveryInfo.quoteItemId;
                        }),

                        newDeliveryData = _.union(rejected, [deliveryInfo]);

                    deliveryDataProvider.deliveryData(newDeliveryData);
                }
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
                        url: Url.build('/delivery-step/storelocator/getstores'),
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
