define([

    'ko',
    'uiComponent',
    'underscore',    
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote'

], function (
    ko,
    Component,
    _,
    customer,
    quote
    ) {
    'use strict';
    var imageData = window.checkoutConfig.imageData;
    var quoteItemData = window.checkoutConfig.quoteItemData;
    return Component.extend({
        defaults: {
            template: 'Dyode_DeliveryMethod/shipping-method'
        },
        imageData: imageData,
        quoteItemData: quoteItemData,
        isLogedIn: customer.isLoggedIn(),
        getItems: ko.observableArray(quote.getItems()),
        getTotals: quote.getTotals(),
        /**
             * @param {Integer} item_id
             * @return {null}
             */
        getSrc: function (item_id) {
            if (this.imageData[item_id]) {
                var src = this.imageData[item_id].src;
                var regex2 = new RegExp(/\/cache\/(\w|\d|)*/, 'gi');
                var ret = src.replace(regex2,'');
                return ret;
            }
                return null;
        },
        getQuote: function() {
            return this.quoteItemData;
        },
        getProductItems: function() {
            var self = this;
            var items = this.getTotals().items;
            var productItems = [];
            items.forEach(function(item) {

                var productItem = {
                    item_id: ko.observable(item.item_id),
                    pid: item.item_id,
                    product_name: item.name,
                    product_price: Number(item.price).toFixed(2),
                    product_qty: item.qty,
                    product_image_url: self.getSrc(item.item_id)
                };
            // Push each product details object to 
            productItems.push(productItem);
            });
            return productItems.length !== 0 ? productItems: null;
        }

    });
});
