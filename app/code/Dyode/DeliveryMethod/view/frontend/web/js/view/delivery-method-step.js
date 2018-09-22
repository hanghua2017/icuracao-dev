define(
    [
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/model/url-builder',
        'mage/storage',
        'mage/translate',
        'Magento_Checkout/js/model/full-screen-loader',
        'Magento_Customer/js/model/customer'
    ],
    function (
        ko,
        Component,
        _,
        stepNavigator,
        quote,
        urlBuilder,
        storage,
        $t,
        fullScreenLoader,
        customer
    ) {
        'use strict';
        /**
        * delivery-method - is the name of the component's .html template
        */
        var imageData = window.checkoutConfig.imageData;
        var storeArray= ko.observableArray([]);;

        return Component.extend({
            defaults: {
                template: 'Dyode_DeliveryMethod/delivery-method'
            },
            imageData: imageData,
            //add here your logic to display step,
            isVisible: ko.observable(true),
            isLogedIn: customer.isLoggedIn(),
            //step code will be used as step content id in the component template
            stepCode: 'deliverySelection',
            //step title value
            stepTitle: 'Delivery Method',
            getItems: ko.observableArray(quote.getItems()),
            getTotals: quote.getTotals(),
            storeList: ko.observableArray([]),

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
            //return product details to be shown on delivery method page
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
                    product_image_url: self.getSrc(item.item_id),
                    state:ko.observable(false),
                    deliveryMethod: 'Shipping',
                    setStorepickup: function(storeElement){
                      var pid = storeElement.pid;
                      var radioValue = jQuery("input[name='delivery-product-"+pid+"']:checked").val();
                      console.log(radioValue);
                      if(radioValue == 'shipping'){
                         storeElement.state(false);
                      } else{
                         storeElement.state(true);
                      }
                    },
            		   selectLocation:function(formelement) {
                      console.log("here");
                      var pid = formelement.pid;
                      var serviceUrl,storeParams;
                      console.log(pid);
                      var zipcode = jQuery("#deliveryform"+pid+" input[name=pickup-zipcode]").val();
                      if(zipcode){
			                  fullScreenLoader.startLoader();
                        jQuery.ajax({
                            url: '/storeloc/storelocator/index',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                zipcode: zipcode,
                                pid: pid,
                            },
                            success: function(response) {
				                        fullScreenLoader.stopLoader();
                                jQuery(".avail-store-outer").html(response);
                                jQuery(".avail-store-outer").applyBindings();
                                jQuery("#dialog-message").dialog({
                                  modal: true,
                                  buttons: {
                                    Ok: function() {
                                      jQuery( this ).dialog( "close" );
                                    }
                                  }
                                });

                            },
                            error: function (xhr, status, errorThrown) {
                                console.log('Error happens. Try again.');
                            },
                            complete: function() {
                				          fullScreenLoader.stopLoader();
                			      }
                        });
                      }

                    },
                    changeLocation:function(){
                      return "th";
                    },


                }
                  productItems.push(productItem);
                });
                return productItems.length !== 0 ? productItems: null;
            },
            updateLocation:function(locElement,e) {
                console.log("updateLocation");
                var location_item = jQuery(locElement.target).closest('.form').find("input").val();
                var res = location_item.split("-");
                var location_id = res[0];
                var item_id = res[1];
                console.log(location_id+item_id);
                fullScreenLoader.startLoader();
                jQuery.ajax({
                    url: '/storeloc/deliverylocation/index',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        location_id: location_id,
                        item_id: item_id,
                    },
                    success: function(response) {
                        jQuery("#deliverydetails"+item_id).html(response);
                        fullScreenLoader.stopLoader();
                        jQuery("#deliveryform"+item_id).css("display","none");
                        jQuery("#deliverydetails"+item_id).parent().css("display","block");
                      //  jQuery(".avail-store-outer").applyBindings();
                        jQuery("#dialog-message").dialog("close");
                      //  console.log(productItems.length);
                    },
                    error: function (xhr, status, errorThrown) {
                        console.log('Error happens. Try again.');
                    },
                    complete: function(){
                        fullScreenLoader.stopLoader();
                    }
                });
           },

            /**
            *
            * @returns {*}
            */
            initialize: function () {

              this._super().observe({
                     deliveryMethod: ko.observable(true),

              });
		      

                // register your step
                stepNavigator.registerStep(
                    this.stepCode,
                    //step alias
                    null,
                    this.stepTitle,
                    //observable property with logic when display step or hide step
                    this.isVisible,

                    _.bind(this.navigate, this),

                    /**
                    * sort order value
                    * 'sort order value' < 10: step displays before shipping step;
                    * 10 < 'sort order value' < 20 : step displays between shipping and payment step
                    * 'sort order value' > 20 : step displays after payment step
                    */
                    5
                );

                return this;
            },

            /**
            * The navigate() method is responsible for navigation between checkout step
            * during checkout. You can add custom logic, for example some conditions
            * for switching to your custom step
            */
            navigate: function () {

            },

            /**
            * @returns void
            */
            navigateToNextStep: function () {
                stepNavigator.next();
            }
        });
    }
);
