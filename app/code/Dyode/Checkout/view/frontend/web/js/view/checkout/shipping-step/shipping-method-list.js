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

    var imageData = window.checkoutConfig.imageData,
        quoteItemData = window.checkoutConfig.quoteItemData;


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
        imageData: imageData,
        quoteItemData: quoteItemData,
        isLogedIn: customer.isLoggedIn(),
        getItems: ko.observableArray(quote.getItems()),
        getTotals: quote.getTotals(),
        shippingTotals: ko.observableArray([]),
        shippingRates: shippingService.getShippingRates(),
        shippingOptions: ko.observableArray([]),
        getRatesForGroup: ko.observableArray([]),

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
         *
         * KnockoutJS: "this" here in getProductItems::foreach context
         */
        selectedShippingMethod: function (model) {
            //collect shipping info based on the shipping method selection
            var shippingInfo = shippingInfoDataProvider.shippingInfo(),
                quoteTotalEntry = _.findWhere(shippingInfo, {
                    quoteItemId: this.item_id
                });

            if (quoteTotalEntry) {

                //updating existing entry with new shipping selection data
                var updatedShippingInfo = _.extend(quoteTotalEntry, {
                    carrier_code: model.carrier_code,
                    method_code: model.method_code
                });

                //updating shippingInfo with the updatedShippingInfo
                _.extend(
                    _.findWhere(shippingInfo, {
                        quoteItemId: this.item_id
                    }),
                    updatedShippingInfo
                );

                //Finally updating global shippingInfo ko observableArray
                shippingInfoDataProvider.shippingInfo(shippingInfo);

            } else {

                //Adding a new entry into shippingInfo ko observableArray
                shippingInfoDataProvider.shippingInfo.push({
                    quoteItemId: this.item_id,
                    carrier_code: model.carrier_code,
                    method_code: model.method_code
                });
            }
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
                var quoteItemId = parseInt(quoteItem.item_id);

                productItems.push({
                    quoteItemId: quoteItemId,
                    productName: quoteItem.name,
                    productPrice: priceUtils.formatPrice(quoteItem.price, quote.getPriceFormat()),
                    productQty: quoteItem.qty,
                    productImgUrl: quoteItem.thumbnail,
                    freight: quoteItem.product.isfreight,

                    shippingRateCollection: self.collectShippingRates,
                    getRatesForGroup: self.getRatesForGroup,

                    //Function bindings
                    updateShippingTotals: self.selectedShippingMethod,
                    //isQuotesAvailable: ko.observable(false),

                    isQuotesAvailable: ko.computed(function () {
                        if (shippingOptions[quoteItemId]) {
                            return true;
                        }

                        return false;
                    }),

                    isStorePickup: ko.computed(function () {
                        var shippingOption = shippingOptions[quoteItemId];

                        if (shippingOption && shippingOption.deliveryOption === 'store_pickup') {
                            return true;
                        }

                        return false;
                    }),

                    shippingMethods: ko.computed(function () {
                        var shippingOption = shippingOptions[quoteItemId],
                            methods = [],
                            checked = true;

                        if (!shippingOption || shippingOption.deliveryOption !== 'ship_to_home') {
                            return methods;
                        }

                        _.each(shippingOption.data, function (shippingMethod) {
                            var carrierMethodCodes = shippingMethod.carrier_code + '_' + shippingMethod.method_code;

                            methods.push(_.extend(shippingMethod, {
                                updateShippingTotals: self.updateShippingTotals.call(self, shippingMethod, quoteItem),
                                isMethodChecked: checked ? carrierMethodCodes : false,
                                methodValue: carrierMethodCodes,
                                methodId: 'method_' + carrierMethodCodes + '_' + quoteItemId,
                                methodName: 'ship_method_' + quoteItemId,
                                methodPrice: priceUtils.formatPrice(shippingMethod.amount, quote.getPriceFormat()),
                                expectedDeliveryMsg: $t('3-5 business days')
                            }));

                            if (checked) { //make the first shipping method selected by default
                                checked = false;
                            }
                        });

                        return methods;
                    }),


                    storePicked: ko.computed(function () {
                        var shippingOption = shippingOptions[quoteItemId],
                            store = {};

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
                    })
                });

            });

            return productItems.length !== 0 ? productItems : null;
        },

        updateShippingTotals: function (shippingMethod, quoteItem) {
        },

        collectShippingRates: function (model) {
            _.each(this.shippingRates, function (rate) {
                if (model.item_id == rate.quote_item_id) {
                    return rate.data;
                }
            });

            return [];
        },

        /**
         * Set shipping method.
         * @param {String} methodData
         * @returns bool
         */
        selectShippingMethod: function (methodData) {
            selectShippingMethodAction(methodData);
            checkoutData.setSelectedShippingRate(methodData['carrier_code'] + '_' + methodData['method_code']);

            return true;
        },
        
        /**
         * Change shipping method for quote items
         */
        changeShippingMethod:  function() {
            stepNavigator.navigateTo('deliverySelection');
        },

        /**
         * Format shipping price.
         * @returns {String}
         */
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        }

    });
});
