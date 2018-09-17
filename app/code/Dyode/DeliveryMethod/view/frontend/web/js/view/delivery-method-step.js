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
        var myObservableArray;

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
            //storeList:ko.observable(true),
            myObservableArray : ko.observableArray(),
          //  state: ko.observable(false),

            /**
             * @param {Integer} item_id
             * @return {null}
             */
            getSrc: function (item_id) {
                if (this.imageData[item_id]) {

                    return this.imageData[item_id].src;
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
                      // console.log("inside test",this,$(e.target));
                    },
            		    assignLocation:function(){
                			 // var stores = [];
                			 // storesList.forEach(function(stores) {
                				// var store = {
                				// 	location_id: stores.location_id
                				// }
                	  		// 	stores.push(store);
                			 // });
                       console.log("assignLocation");
                       myObservableArray.push(1);
            		    },
		               selectLocation:function(formelement) {
                      console.log("here");
                      var pid = formelement.pid;
                      var serviceUrl,storeParams;
			                var storeArr = ko.observableArray();
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
                                var storeResponse = jQuery.map(response, function(value, index) {
                                    return [value];
                                });
                                console.log(storeResponse);

				//assignLocation(storeResponse);

                            //    this.myObservableArray.push(storeResponse);
				//storeArr.push(x);
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
                            complete: function(){
				fullScreenLoader.stopLoader();
			    }
                        });
                      }

                    },

                }
                  productItems.push(productItem);
                });
                return productItems.length !== 0 ? productItems: null;
            },
            updateLocation:function(locElement,e){
                var location_id = jQuery(locElement.target).closest('.form').find("input#location_id").val();
                var item_id = jQuery(locElement.target).closest('.form').find("input#item_id").val();
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
                        fullScreenLoader.stopLoader();
                        console.log(response);

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
