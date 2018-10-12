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

define(['uiRegistry'], function (registry) {

    return {
        addressStepShippingComponentName: 'checkout.steps.address-step.shippingAddress',
        shippingAddressComponent: null,

        /**
         * Collecting shipping-step address components to perform their validation.
         */
        initialize: function () {
            var shippingComponent = registry.get(this.addressStepShippingComponentName);

            if (shippingComponent) {
                this.shippingAddressComponent = shippingComponent;
            }
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
            return true;
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
