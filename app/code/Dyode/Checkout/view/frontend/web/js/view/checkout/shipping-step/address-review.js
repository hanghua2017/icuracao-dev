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
        addressRegionOptions = addressFiledsetComponent.source.dictionaries.region_id,

        /**
         * Shipping address related variables
         */
        hasShipAddressStreetLine1 = ko.computed(function () {
            if (!shippingAddress().street || !shippingAddress().street[0]) {
                return false;
            }

            return true;
        }),

        shipAddressStreetLine1 = ko.computed(function () {
            if (hasShipAddressStreetLine1()) {
                return shippingAddress().street[0];
            }

            return '';
        }),

        hasShipAddressStreetLine2 = ko.computed(function () {
            if (!shippingAddress().street || !shippingAddress().street[1]) {
                return false;
            }

            return true;
        }),

        shipAddressStreetLine2 = ko.computed(function () {
            if (hasShipAddressStreetLine1()) {
                return shippingAddress().street[1];
            }

            return '';
        }),

        /**
         * Billing Related variables
         */
        hasBillAddressStreetLine1 = ko.computed(function () {
            if (!shippingAddress().street || !shippingAddress().street[0]) {
                return false;
            }

            return true;
        }),

        billAddressStreetLine1 = ko.computed(function () {
            if (hasBillAddressStreetLine1()) {
                return shippingAddress().street[0];
            }

            return '';
        }),

        hasBillAddressStreetLine2 = ko.computed(function () {
            if (!shippingAddress().street || !shippingAddress().street[1]) {
                return false;
            }

            return true;
        }),

        billAddressStreetLine2 = ko.computed(function () {
            if (hasBillAddressStreetLine1()) {
                return shippingAddress().street[1];
            }

            return '';
        });

    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/shipping-step/address-review'
        },
        hasShippingAddress: shippingAddress() ? true : false,
        hasShipAddressStreetLine1: hasShipAddressStreetLine1,
        shipAddressStreetLine1: shipAddressStreetLine1,
        hasShipAddressStreetLine2: hasShipAddressStreetLine2,
        shipAddressStreetLine2: shipAddressStreetLine2,

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
        hasBillingAddress: billingAddress() ? true : false,
        hasBillAddressStreetLine1: hasBillAddressStreetLine1,
        billAddressStreetLine1: billAddressStreetLine1,
        hasBillAddressStreetLine2: hasBillAddressStreetLine2,
        billAddressStreetLine2: billAddressStreetLine2,

        billAddressFullName: ko.computed(function () {
            return shippingAddress().firstname + ' ' + shippingAddress().lastname;
        }),

        billAddressCity: ko.computed(function () {
            return shippingAddress().city;
        }),

        billAddressRegion: ko.computed(function () {
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

        billAddressCountry: ko.computed(function () {
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

        billAddressPhone: ko.computed(function () {
            return shippingAddress().telephone;
        }),
    });
});
