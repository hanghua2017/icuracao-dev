/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 */
define([
        'ko',
        'uiComponent',
        'underscore',
        'Magento_Checkout/js/model/step-navigator'
], function (
        ko,
        Component,
        _,
        stepNavigator
) {
        'use strict';

        return Component.extend({
            defaults: {
                template: 'Dyode_ShippingStep/shipping-method'
            },
            isVisible: ko.observable(true),

            /**
            *
            * @returns {*}
            */
            initialize: function () {
                this._super();
                stepNavigator.registerStep(
                    'shipping_method',
                    'shipping_method',
                    'Lorem Ipsum',
                    this.isVisible,
                    _.bind(this.navigate, this),
                    8
                );

                return this;
            },

            navigate: function () {},

            navigateToNextStep: function () {
                stepNavigator.next();
            }
        });
    }
);