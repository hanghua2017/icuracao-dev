define(
    [
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator',
        'Magento_Checkout/js/model/quote',
        'Magento_Customer/js/model/customer'
    ],
    function (
        ko,
        Component,
        _,
        stepNavigator,
        quote,
        customer
    ) {
        'use strict';
        /**
        * check-login - is the name of the component's .html template
        */
        return Component.extend({
            defaults: {
                template: 'Dyode_Checkout/curacao-payment'
            },

            //add here your logic to display step,
            visible: ko.observable(!quote.isVirtual()),
            isVisible: ko.observable(quote.isVirtual()),
            isLogedIn: customer.isLoggedIn(),
            //step code will be used as step content id in the component template
            stepCode: 'checkout-curacao-step',
            //step title value
            stepTitle: 'Payment',

            /**
            *
            * @returns {*}
            */
            initialize: function () {
                this._super();
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
                    15
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
