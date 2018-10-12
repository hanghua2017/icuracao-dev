/**
 * Dyode_CheckoutAddressStep Magento2 Module.
 *
 * Adding new checkout step in the one page checkout.
 *
 * @module    Dyode_CheckoutAddressStep
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define([
    'uiRegistry',
    'Dyode_CheckoutAddressStep/js/data/address-data-provider'
], function (registry, addressDataProvider) {

    return {
        addressStepShippingComponentName: 'checkout.steps.address-step.shippingAddress',
        addressStepBillingComponentName: 'checkout.steps.address-step.shippingAddress.billing-address',
        shippingAddressComponent: null,
        billingAddressComponent: null,

        /**
         * Collecting shipping-step address components to perform their validation.
         *
         * @returns {exports}
         */
        initialize: function () {
            if (this.billingAddressComponent && this.shippingAddressComponent) {
                return this;
            }

            var shippingComponent = registry.get(this.addressStepShippingComponentName),
                billingComponent = registry.get(this.addressStepBillingComponentName);

            if (shippingComponent) {
                this.shippingAddressComponent = shippingComponent;
            }

            if (billingComponent) {
                this.billingAddressComponent = billingComponent;
            }

            return this;
        },

        /**
         * Checks whether shipping address is valid or not.
         *
         * @returns {Boolean}
         */
        isShippingAddressValid: function () {
            if (!this.shippingAddressComponent) {
                return false;
            }

            return this.shippingAddressComponent.validateShippingInformation();
        },

        /**
         * Checks whether billing address is valid or not.
         *
         * @returns {Boolean}
         */
        isBillingAddressValid: function () {
            if (!this.billingAddressComponent) {
                return false;
            }

            if (addressDataProvider.isBillingSameAsShipping()) {
                return true;
            }

            var $this = this.billingAddressComponent;

            $this.source.set('params.invalid', false);
            $this.source.trigger($this.dataScopePrefix + '.data.validate');

            if ($this.source.get($this.dataScopePrefix + '.custom_attributes')) {
                $this.source.trigger($this.dataScopePrefix + '.custom_attributes.data.validate');
            }

            if (!$this.source.get('params.invalid')) {
                return true;
            }

            $this.focusInvalid();

            return false;
        },

        /**
         * Validate both shipping and billing address in the address-step.
         *
         * @returns {Boolean}
         */
        validateAddresses: function () {
            this.initialize();

            if (!this.isShippingAddressValid()) {
                return false;
            }

            if (!this.isBillingAddressValid()) {
                return false;
            }

            return true;
        }
    };
});
