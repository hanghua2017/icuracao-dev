/*global define*/
define([
    'jquery',
    'ko',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'Magento_Ui/js/form/form',
    'mage/storage',
    'mage/translate',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/action/get-payment-information',
    'Magento_Checkout/js/model/totals',
    'Magento_Ui/js/model/messageList',
    'Magento_Customer/js/model/customer'
], function(
      $,
      ko,
      quote,
      urlBuilder,
      Component,
      storage,
      $t,
      fullScreenLoader,
      getPaymentInformationAction,
      totals,
      messageList,
      customer
    ) {
    'use strict';

    var customerData = window.customerData;
    var limit = window.checkoutConfig.limit;
    var dpayment = window.checkoutConfig.total;
    var canapply = window.checkoutConfig.canapply;

    return Component.extend({

        isLogedIn: customer.isLoggedIn(),
        customerData:customerData,
        dpayment:dpayment,
        limit:limit,
        canapply:canapply,

        getDiscount: function () {
              var self = this;
              var message = $t('Your store credit was successfully applied');

              messageList.clear();
              fullScreenLoader.startLoader();
          return storage.post(
          urlBuilder.createUrl('/checkout/apply', {})
          ).done(function (response) {
            console.log("hi");
            var deferred;

            if (response) {
                deferred = $.Deferred();
                totals.isLoading(true);
                getPaymentInformationAction(deferred);
                $.when(deferred).done(function () {
                    totals.isLoading(false);
                });
                messageList.addSuccessMessage({
                    'message': message
                });
            }
          }).always(function () {
            fullScreenLoader.stopLoader();
          });
        },
        removeDiscount:function(){
          fullScreenLoader.startLoader();
          var self = this;
          return storage.delete(
            urlBuilder.createUrl('/checkout/remove', {})
            ).done(
               function (response) {
                   console.log(response);
                   var deferred;
            if (response) {
                deferred = $.Deferred();
                totals.isLoading(true);
                getPaymentInformationAction(deferred);
                $.when(deferred).done(function () {
                    totals.isLoading(false);
                    fullScreenLoader.stopLoader();
                });
                messageList.addSuccessMessage({
                    'message': message
                });
            }
               }
           ).fail(
               function (response) {
                //   alert(response);
               }
           ).always(function () {
            fullScreenLoader.stopLoader();
          });
        },
        getCuracaoId:function(){
          if(this.customerData.custom_attributes.curacaocustid.value){
              var curacaoid = this.customerData.custom_attributes.curacaocustid.value;
              var last4digits = curacaoid.slice(-4);
              return last4digits;
          }
          return null;
        },

        getCreditLimit:function(){
          return this.limit;
        },
        getDownPayment:function(){
          return this.dpayment;
        },
        initialize: function () {
          this._super()
             .observe({
                 ApplyDiscount: ko.observable(true)
             });

           this.ApplyDiscount.subscribe(function (newValue) {
               if(newValue){
                    console.log('checked'+newValue);
                    this.getDiscount();
               }else{
                   console.log('Unchecked'+newValue);
                   this.removeDiscount();
               }
           },this);
            //this.getDiscount();
            console.log(customer);
            // component initialization logic
            return this;
        },
    });
});
