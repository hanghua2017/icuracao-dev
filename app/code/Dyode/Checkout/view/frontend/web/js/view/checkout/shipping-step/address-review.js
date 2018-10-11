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

    /**
     * Collecting country options and region options from the shipping address field-set component.
     * @todo this section can be improved by bringing both country and region list directly to the component.
     */
    var shippingAddressFieldset = 'checkout.steps.address-step.shippingAddress.shipping-address-fieldset',
        addressFiledsetComponent = registry.get(shippingAddressFieldset),
        addressCountryOptions = addressFiledsetComponent.source.dictionaries.country_id,
        addressRegionOptions = addressFiledsetComponent.source.dictionaries.region_id;

    addressDataProvider.shippingAddress.su

    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/shipping-step/address-review'
        },
        hasShippingAddress: ko.observable(false),
        hasShipAddressStreetLine1: ko.observable(false),
        hasShipAddressStreetLine2: ko.observable(false),
        hasShippingAddressRegion: ko.observable(false),
        hasShippingAddressCountry: ko.observable(false),

        shipAddressStreetLine1: ko.observable(null),
        shipAddressStreetLine2: ko.observable(null),
        shipAddressFullName: ko.observable(null),
        shipAddressCity: ko.observable(null),
        shipAddressRegion: ko.observable(null),
        shipAddressCountry: ko.observable(null),
        shipAddressPhone: ko.observable(null),

        hasBillingAddress: ko.observable(false),
        hasBillAddressStreetLine1: ko.observable(false),
        hasBillAddressStreetLine2: ko.observable(false),
        hasBillingAddressRegion: ko.observable(false),
        hasBillingAddressCountry: ko.observable(false),

        billAddressFullName: ko.observable(null),
        billAddressCity: ko.observable(null),
        billAddressPhone: ko.observable(null),
        billAddressStreetLine1: ko.observable(null),
        billAddressStreetLine2: ko.observable(null),
        billAddressRegion: ko.observable(null),
        billAddressCountry: ko.observable(null),

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super();

            this.subscribeShippingAddress();
            this.subscribeBillingAddress();
            return this;
        },

        /**
         * Subscribe to shipping address change
         */
        subscribeShippingAddress: function () {
            var self = this;

            addressDataProvider.shippingAddress.subscribe(function (shippingAddress) {
                if (!shippingAddress) {
                    shippingAddress = {};
                }

                self.hasShippingAddress(shippingAddress.firstname);
                self.shipAddressFullName(shippingAddress.firstname + ' ' + shippingAddress.lastname);
                self.shipAddressCity(shippingAddress.city);
                self.shipAddressPhone(shippingAddress.telephone);

                if (shippingAddress.street && shippingAddress.street[0]) {
                    self.hasShipAddressStreetLine1(true);
                    self.shipAddressStreetLine1(shippingAddress.street[0]);
                } else {
                    self.hasShipAddressStreetLine1(false);
                    self.shipAddressStreetLine1(null);
                }

                if (shippingAddress.street && shippingAddress.street[1]) {
                    self.hasShipAddressStreetLine2(true);
                    self.shipAddressStreetLine2(shippingAddress.street[1]);
                } else {
                    self.hasShipAddressStreetLine2(false);
                    self.shipAddressStreetLine1(null);
                }

                if (shippingAddress.region_id) {
                    self.hasShippingAddressRegion(true);
                    self.shipAddressRegion(self.addressRegionLabel(shippingAddress));
                } else {
                    self.hasShippingAddressRegion(false);
                    self.shipAddressRegion(null);
                }

                if (shippingAddress.country_id) {
                    self.hasShippingAddressCountry(true);
                    self.shipAddressCountry(self.addressCountryLabel(shippingAddress));
                } else {
                    self.hasShippingAddressCountry(false);
                    self.shipAddressCountry(null);
                }

            });
        },

        /**
         * Subscribe to billing address change
         */
        subscribeBillingAddress: function () {
            var self = this;

            addressDataProvider.billingAddress.subscribe(function (billingAddress) {
                if (!billingAddress) {
                    billingAddress = {};
                }

                self.hasBillingAddress(billingAddress.firstname);
                self.billAddressFullName(billingAddress.firstname + ' ' + billingAddress.lastname);
                self.billAddressCity(billingAddress.city);
                self.billAddressPhone(billingAddress.telephone);

                if (billingAddress.street && billingAddress.street[0]) {
                    self.hasBillAddressStreetLine1(true);
                    self.billAddressStreetLine1(billingAddress.street[0]);
                } else {
                    self.hasBillAddressStreetLine1(false);
                    self.billAddressStreetLine1(null);
                }

                if (billingAddress.street && billingAddress.street[1]) {
                    self.hasBillAddressStreetLine2(true);
                    self.billAddressStreetLine2(billingAddress.street[1]);
                } else {
                    self.hasBillAddressStreetLine2(false);
                    self.billAddressStreetLine1(null);
                }

                if (billingAddress.region_id) {
                    self.hasBillingAddressRegion(true);
                    self.billAddressRegion(self.addressRegionLabel(billingAddress));
                } else {
                    self.hasBillingAddressRegion(false);
                    self.billAddressRegion(null);
                }

                if (billingAddress.country_id) {
                    self.hasBillingAddressCountry(true);
                    self.billAddressCountry(self.addressCountryLabel(billingAddress));
                } else {
                    self.hasBillingAddressCountry(false);
                    self.billAddressCountry(null);
                }

            });
        },

        addressRegionLabel: function (address) {
            if (addressRegionOptions) {
                var selectedRegion = _.findWhere(addressRegionOptions, {
                    value: address.region_id,
                    country_id: address.country_id
                });

                if (selectedRegion) {
                    return selectedRegion.label;
                }
            }

            return null;
        },

        addressCountryLabel: function (address) {
            if (addressCountryOptions) {
                var selectedCountry = _.findWhere(addressCountryOptions, {
                    value: address.country_id
                });

                if (selectedCountry) {
                    return selectedCountry.label;
                }
            }

            return null;
        }

    });
});
