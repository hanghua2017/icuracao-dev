/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       02/10/2018
 */

'use strict';

define([
    'ko',
    'uiComponent',
    'underscore',
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/action/select-shipping-method',
    'Magento_Catalog/js/price-utils',
    'Magento_Checkout/js/model/shipping-service', // Shipping service
    'Magento_Checkout/js/checkout-data', //checkoutData
    'Magento_Checkout/js/model/shipping-save-processor',
    './shipping-data-provider',
    './shipping-info-save-processor'

], function (
    ko,
    Component,
    _,
    customer,
    quote,
    selectShippingMethodAction,
    priceUtils,
    shippingService, // Shipping Service
    checkoutData,
    checkoutShippingSaveProcessor,
    shippingInfoDataProvider,
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
                template: 'Dyode_CheckoutDeliveryMethod/shipping-method'
            },
            imageData: imageData,
            quoteItemData: quoteItemData,
            isLogedIn: customer.isLoggedIn(),
            getItems: ko.observableArray(quote.getItems()),
            getTotals: quote.getTotals(),

            /**
             * Fires when a shipping method option is selected against a quote item
             * in the shipping step.
             * 
             * KnockoutJS: "this" here in getProductItems::foreach context
             */
            selectedShippingMethod: function (model) {

                //collect shipping info based on the shipping method selection
                var shippingInfo = shippingInfoDataProvider.shippingInfo(),
                    quoteTotalEntry = _.findWhere(shippingInfo, {quoteItemId: this.item_id});

                if (quoteTotalEntry) {

                    //updating existing entry with new shipping selection data
                    var updatedShippingInfo = _.extend(quoteTotalEntry, {
                        carrier_code: model.carrier_code,
                        method_code: model.method_code,
                    });

                    //updating shippingInfo with the updatedShippingInfo
                    _.extend(
                        _.findWhere(shippingInfo, {quoteItemId: this.item_id}),
                        updatedTotalEntry
                    );

                    //Finally updating global shippingInfo ko observableArray
                    shippingInfoDataProvider.shippingInfo(shippingInfo);

                } else {

                    //Adding a new entry into shippingInfo ko observableArray
                    shippingInfoDataProvider.shippingInfo.push({
                        quoteItemId: this.item_id,
                        carrier_code: model.carrier_code,
                        method_code: model.method_code,
                    });
                }
            },

            /**
             * getSrc
             */
            getSrc: function (item_id) {
                if (this.imageData[item_id]) {
                    var src = this.imageData[item_id].src;
                    var regex2 = new RegExp(/\/cache\/(\w|\d|)*/, 'gi');
                    var ret = src.replace(regex2, '');
                    return ret;
                }
                return null;
            },

            /**
             * getQuote
             */
            getQuote: function () {
                return this.quoteItemData;
            },

            /** 
            * Returns the shipping methods 
            */
            shippingRates: shippingService.getShippingRates(),
            shippingRateGroups: ko.observableArray([]),
            getRatesForGroup: ko.observableArray([]),

            /**
            * @override
            */
            initObservable: function () {
                var self = this;

                this._super();

                this.shippingRates.subscribe(function (rates) {
                    self.shippingRateGroups([]);
                    _.each(rates, function (rate) {
                        var carrierTitle = rate['carrier_title'];

                        if (self.shippingRateGroups.indexOf(carrierTitle) === -1) {
                            self.shippingRateGroups.push(carrierTitle);
                        }
                    });
                });

                return this;
            },

            /**
             * Prepare quote item data with which we are showing the entire
             * shipping method options section
             */
            getProductItems: function () {
                var self = this,
                    productItems = [];

                _.each(quoteItemData, function (quoteItem) {
                    productItems.push({
                        item_id: quoteItem.item_id,
                        product_name: quoteItem.name,
                        product_price: priceUtils.formatPrice(quoteItem.price).toString(),
                        product_qty: quoteItem.qty,
                        product_image_url: self.getSrc(quoteItem.item_id),
                        shipping_methods: self.shippingRates,
                        freight: quoteItem.product.isfreight,
                        shippingRateGroups: self.shippingRateGroups,
                        getRatesForGroup: self.getRatesForGroup,

                        //Function bindings
                        updateShippingTotals: self.selectedShippingMethod,
                    });

                });
                return productItems.length !== 0 ? productItems : null;
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
            * Get shipping rates for specific group based on title.
            * @returns Array
            */
            getRatesForGroup: function (shippingRateGroupTitle) {
                var self = this;
                return _.filter(self.shipping_methods(), function (rate) {
                    return shippingRateGroupTitle === rate['carrier_title'];
                });
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
