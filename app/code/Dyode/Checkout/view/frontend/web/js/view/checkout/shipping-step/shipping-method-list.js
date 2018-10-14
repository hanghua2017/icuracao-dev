/**
 * Dyode_Checkout Magento 2 module
 *
 * Extending Magento_Checkout
 *
 * @module  Dyode_Chekout
 * @author  Rajeev <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define([
    'ko',
    'uiComponent',
    'underscore',
    'mage/translate',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/shipping-service', // Shipping service
    'Magento_Checkout/js/checkout-data', //checkoutData
    'Magento_Checkout/js/model/shipping-save-processor',
    'Magento_Tax/js/view/checkout/shipping_method/price',
    'Dyode_Checkout/js/view/model/shipping-data-provider',
    'Magento_Checkout/js/model/step-navigator',
    'Dyode_Checkout/js/view/model/shipping-info-save-processor'

], function (
    ko,
    Component,
    _,
    $t,
    customer,
    quote,
    selectShippingMethodAction,
    priceUtils,
    shippingService, // Shipping Service
    checkoutData,
    checkoutShippingSaveProcessor,
    shippingPrice,
    shippingInfoDataProvider,
    stepNavigator,
    customShippingInfoSaveProcessor
) {

    var quoteItemData = window.checkoutConfig.quoteItemData;

    /**
     * Here we are registering custom processor in order to deal
     * with both guest and customer type shipping data.
     */
    checkoutShippingSaveProcessor.registerProcessor(
        'new-customer-address', customShippingInfoSaveProcessor
    );

    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/shipping-step/shipping-method-list'
        },
        quoteItemData: quoteItemData,
        isLogedIn: customer.isLoggedIn(),
        getItems: ko.observableArray(quote.getItems()),
        getTotals: quote.getTotals(),
        shippingTotals: ko.observableArray([]),
        shippingRates: shippingService.getShippingRates(),
        shippingOptions: ko.observableArray([]),

        /**
         * @override
         */
        initObservable: function () {
            var self = this;

            this._super();

            this.shippingRates.subscribe(function (rates) {
                var newOptions = [];

                _.each(rates, function (rate) {
                    if (rate.quote_item_id) {
                        var quoteItemId = rate.quote_item_id;

                        newOptions[quoteItemId] = {
                            deliveryOption: rate.delivery_option,
                            data: rate.delivery_option === 'store_pickup' ? rate.store_info : rate.delivery_methods
                        };

                    }
                });

                self.shippingOptions(newOptions);
            });

            return this;
        },

        /**
         * Fires when a shipping method option is selected against a quote item
         * in the shipping step.
         */
        selectedShippingMethod: function (model) {
            var parentComponent = this.parent,
                quoteItemId = model.quote_item_id,
                shippingInfo = {
                    carrier_code: model.carrier_code,
                    method_code: model.method_code,
                    amount: model.amount
                };

            parentComponent.updateShippingInfo(quoteItemId, shippingInfo);

            return true;
        },

        /**
         * Prepare quote item data with which we are showing the entire
         * shipping method options section
         */
        getProductItems: function () {
            var self = this,
                productItems = [],
                shippingOptions = self.shippingOptions();

            _.each(quoteItemData, function (quoteItem) {

                if (quoteItem.product_type === 'virtual') {
                    return false;
                }

                var quoteItemId = parseInt(quoteItem.item_id),
                    shippingOption = shippingOptions[quoteItemId],
                    isQuotesAvailable = !!shippingOption,
                    isStorePickup = !!shippingOption && shippingOption.deliveryOption === 'store_pickup',
                    firstShippingMethod = shippingOption ? _.first(shippingOption.data) : false,
                    carrierCode = firstShippingMethod ? firstShippingMethod.carrier_code : '',
                    methodCode = firstShippingMethod ? firstShippingMethod.method_code : '',
                    activeMethod = firstShippingMethod ? carrierCode + '_' + methodCode : false,
                    shippingMethods = self.prepareShippingMethods(quoteItemId, shippingOption, activeMethod) || [],
                    storePicked = self.prepareStorePicked(shippingOption);

                productItems.push({
                    parent: self,
                    quoteItemId: quoteItemId,
                    productName: quoteItem.name,
                    productPrice: priceUtils.formatPrice(quoteItem.price, quote.getPriceFormat()),
                    productQty: quoteItem.qty,
                    productImgUrl: quoteItem.thumbnail,
                    isQuotesAvailable: ko.observable(isQuotesAvailable),
                    canShowShipMethods: ko.observable(isQuotesAvailable && !isStorePickup),
                    canShowStorePicked: ko.observable(isQuotesAvailable && isStorePickup),
                    isStorePickup: ko.observable(isStorePickup),
                    activeShippingMethod: activeMethod,
                    shippingMethods: shippingMethods,
                    storePicked: storePicked,

                    //function bindings
                    updateShippingInfo: self.selectedShippingMethod
                });
            });

            return productItems;
        },

        /**
         * Prepare shipping methods based on the quote item.
         *
         * @param {String|Integer} quoteItemId
         * @param {Object} shippingMethodCollection
         * @param {String} activeMethod
         * @returns {Array}
         */
        prepareShippingMethods: function (quoteItemId, shippingMethodCollection, activeMethod) {
            var methods = [],
                carrierMethodCode;

            if (!shippingMethodCollection || shippingMethodCollection.deliveryOption !== 'ship_to_home') {
                return methods;
            }

            _.each(shippingMethodCollection.data, function (shippingMethod) {
                carrierMethodCode = shippingMethod.carrier_code + '_' + shippingMethod.method_code;

                methods.push(_.extend(shippingMethod, {
                    methodValue: carrierMethodCode,
                    methodId: 'method_' + carrierMethodCode + '_' + quoteItemId,
                    methodName: 'ship_method_' + quoteItemId,
                    methodPrice: priceUtils.formatPrice(shippingMethod.amount, quote.getPriceFormat()),
                    expectedDeliveryMsg: $t('3-5 business days')
                }));
            });

            return methods;
        },

        /**
         * Store selected against the quote Item in the delivery step.
         *
         * @param {Object} shippingOption
         * @returns {Object}
         */
        prepareStorePicked: function (shippingOption) {
            var store = {};

            if (!shippingOption || shippingOption.deliveryOption !== 'store_pickup') {
                return store;
            }

            store = shippingOption.data;

            return {
                name: store.name,
                id: store.id,
                code: store.code,
                zip: store.address.zip,
                addressLine1: $t(store.address.street),
                addressLine2: $t(store.address.city) + ', ' + store.address.zip
            };
        },

        /**
         * Update shipping info data based on the shipping option selection
         *
         * @param {String|Integer} quoteItemId - Quote item of which shipping data need to be updated
         * @param {Object} updateData - New shipping info.
         */
        updateShippingInfo: function (quoteItemId, updateData) {

            //collect shipping info based on the shipping method selection
            var shippingInfo = shippingInfoDataProvider.shippingInfo(),
                shippingInfoEntry = _.findWhere(shippingInfo, {
                    quote_item_id: quoteItemId
                });

            if (shippingInfoEntry) {

                //updating existing entry with new shipping selection data
                shippingInfoEntry.shipping_data = updateData;

                //updating shippingInfo with the updatedShippingInfo
                _.extend(
                    _.findWhere(shippingInfo, {
                        quote_item_id: quoteItemId
                    }),
                    shippingInfoEntry
                );

                //Finally updating global shippingInfo ko observableArray
                shippingInfoDataProvider.shippingInfo(shippingInfo);

            } else {

                //Adding a new entry into shippingInfo ko observableArray
                shippingInfoDataProvider.shippingInfo.push({
                    quote_item_id: quoteItemId,
                    shipping_type: 'ship_to_home',
                    shipping_data: updateData
                });
            }
        },

        /**
         * Change shipping method for quote items
         */
        changeShippingMethod: function () {
            stepNavigator.navigateTo('deliverySelection');
        }
    });
});
