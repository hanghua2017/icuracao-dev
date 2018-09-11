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
            storeList:ko.observable(true),
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
                    selectLocation:function(formelement) {
                      console.log("here");
                      var pid = formelement.pid;
                      var serviceUrl,storeParams;
                      console.log(pid);
                      var zipcode = jQuery("#deliveryform"+pid+" input[name=pickup-zipcode]").val();
                      if(zipcode){
                         jQuery.ajax({
                            url: '/storeloc/storelocator/index',
                            type: 'POST',
                            dataType: 'json',
                            data: {
                                zipcode: zipcode,
                                pid: pid,
                            },
                            success: function(response) {
                                console.log(response);
                                var storeResponse = jQuery.map(response, function(value, index) {
                                    return [value];
                                });
                                // console.log(array);
                                // var x=[];
                                // jQuery.each(response, function(i,n) {
                                //     x.push(n);
                                // });
                                console.log(storeResponse);

                                jQuery("#dialog-message" ).dialog({
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
                            }
                        });
                      }

                    },

                }
                  productItems.push(productItem);
                });
                return productItems.length !== 0 ? productItems: null;
            },
            assignStores:function(storesList){

            },
            storeLocation: function(formelement,id) {
              console.log(id);
               // this.source.trigger('deliveryform'+id+'.data.validate');
               // var formData = this.source.get('deliveryform'+id);
               // // do something with form data
               // console.dir(formData);
              // jQuery("#dialog-message" ).dialog({
              //   modal: true,
              //   buttons: {
              //     Ok: function() {
              //       jQuery( this ).dialog( "close" );
              //     }
              //   }
              // });
            },
            /**
            *
            * @returns {*}
            */
            initialize: function () {

              this._super().observe({
                     deliveryMethod: ko.observable(true),

              });

              /*     this.deliveryMethod.subscribe(function (newValue) {
                   if(newValue === "StorePickup"){
                        console.log('checked'+newValue);
                        this.state = true;
                        console.log("If:"+(this.state? "Yes":"No"));
                   } else {
                       this.state = false;
                       console.log("Else:"+(this.state? "Yes":"No"));
                   }
                   console.log("inside"+this.state);
                 },this);
                 console.log("otside"+this.state);*/
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
            onSubmit: function() {
                // trigger form validation
                this.source.set('params.invalid', false);
                this.source.trigger('customCheckoutForm.data.validate');

                // verify that form data is valid
                if (!this.source.get('params.invalid')) {
                    // data is retrieved from data provider by value of the customScope property
                    var formData = this.source.get('customCheckoutForm');
                    // do something with form data
                    console.dir(formData);
                }
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
