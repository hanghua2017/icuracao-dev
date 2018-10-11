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
    'underscore',
    'uiComponent',
    'uiRegistry',
    'Magento_Checkout/js/checkout-data',
    'Dyode_CheckoutAddressStep/js/data/address-data-provider'
], function (ko, _, Component, registry, checkoutData, addressDataProvider) {

    var shippingAddress = addressDataProvider.shippingAddress,
        billingAddress = addressDataProvider.billingAddress,

        /**
         * Collecting country options and region options from the shipping address field-set component.
         * @todo this section can be improved by bringing both country and region list directly to the component.
         */
        shippingAddressFieldset = 'checkout.steps.address-step.shippingAddress.shipping-address-fieldset',
        addressFiledsetComponent = registry.get(shippingAddressFieldset),
        addressCountryOptions = addressFiledsetComponent.source.dictionaries.country_id,
        addressRegionOptions = addressFiledsetComponent.source.dictionaries.region_id;

    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/shipping-step/address-review'
        },
        shippingAddress: shippingAddress,
        hasShipAddressStreetLine1: ko.computed(function () {
            if (!shippingAddress().street || !shippingAddress().street[0]) {
                return false;
            }

            return true;
        }),
        shipAddressStreetLine1: ko.computed(function () {
            if (!shippingAddress().street || !shippingAddress().street[0]) {
                return null;
            }

            return shippingAddress().street[0];
        }),
        hasShipAddressStreetLine2: ko.computed(function () {
            if (!shippingAddress().street || !shippingAddress().street[1]) {
                return false;
            }

            return true;
        }),
        shipAddressStreetLine2: ko.computed(function () {
            if (!shippingAddress().street || !shippingAddress().street[1]) {
                return null;
            }

            return shippingAddress().street[1];
        }),
        shipAddressFullName: ko.computed(function () {
            return shippingAddress().firstname + ' ' + shippingAddress().lastname;
        }),
        shipAddressCity: ko.computed(function () {
            return shippingAddress().city;
        }),
        shipAddressRegion: ko.computed(function () {
            if (addressRegionOptions) {
                var selectedRegion = _.findWhere(addressRegionOptions, {
                    value: shippingAddress().region_id,
                    country_id: shippingAddress().country_id
                });

                if (selectedRegion) {
                    return selectedRegion.label;
                }
            }

            return null;
        }),
        shipAddressCountry: ko.computed(function () {
            if (addressCountryOptions) {
                var selectedCountry = _.findWhere(addressCountryOptions, {
                    value: shippingAddress().country_id
                });

                if (selectedCountry) {
                    return selectedCountry.label;
                }
            }

            return null;
        }),

        shipAddressPhone: ko.computed(function () {
            return shippingAddress().telephone;
        }),

        /**
         * Billing adderss related component properties.
         */
        billingAddress: billingAddress,
        hasBillAddressStreetLine1: ko.computed(function () {
            if (!billingAddress().street || !billingAddress().street[0]) {
                return false;
            }

            return true;
        }),
        billAddressStreetLine1: ko.computed(function () {
            if (!billingAddress().street || !billingAddress().street[0]) {
                return null;
            }

            return billingAddress().street[0];
        }),
        hasBillAddressStreetLine2: ko.computed(function () {
            if (!billingAddress().street || !billingAddress().street[1]) {
                return false;
            }

            return true;
        }),

        billAddressStreetLine2: ko.computed(function () {
            if (!billingAddress().street || !billingAddress().street[1]) {
                return null;
            }

            return billingAddress().street[1];
        }),

        billAddressFullName: ko.computed(function () {
            return billingAddress().firstname + ' ' + billingAddress().lastname;
        }),

        billAddressCity: ko.computed(function () {
            return billingAddress().city;
        }),

        billAddressRegion: ko.computed(function () {
            if (addressRegionOptions) {
                var selectedRegion = _.findWhere(addressRegionOptions, {
                    value: billingAddress().region_id,
                    country_id: billingAddress().country_id
                });

                if (selectedRegion) {
                    return selectedRegion.label;
                }
            }

            return null;
        }),

        billAddressCountry: ko.computed(function () {
            if (addressCountryOptions) {
                var selectedCountry = _.findWhere(addressCountryOptions, {
                    value: billingAddress().country_id
                });

                if (selectedCountry) {
                    return selectedCountry.label;
                }
            }

            return null;
        }),

        billAddressPhone: ko.computed(function () {
            return billingAddress().telephone;
        })
    });
});
