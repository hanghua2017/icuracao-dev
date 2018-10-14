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
    'jquery',
    'uiRegistry',
    'Magento_Checkout/js/checkout-data',
    'Dyode_CheckoutAddressStep/js/data/address-data-provider',
    'Magento_Customer/js/model/customer'
], function ($, registry, checkoutData, addressDatProvider,customer) {

    /**
     * Mixin for Magento_Checkout/js/view/billing-address
     *
     * Disabling billing address form initially visible.
     *
     * @type {{initialize: initialize}}
     */
    var mixin = {

        isCustomerLoggedIn: customer.isLoggedIn,
        /**
         * Make billing form initially invisible.
         * By default, billing address = shipping address.
         */
        initialize: function () {
            this._super();
            this.isAddressFormVisible(false);
            this.isAddressSameAsShipping(addressDatProvider.isBillingSameAsShipping());
            this.isAddressDetailsVisible(true);
            
            this.isAddressSameAsShipping.subscribe(function (newStatus) {
                addressDatProvider.isBillingSameAsShipping(newStatus);
            });

            registry.async('checkoutProvider')(function (checkoutProvider) {
                var billingAddressData = checkoutData.getBillingAddressFromData();

                if (billingAddressData) {
                    checkoutProvider.set(
                        'billingAddress',
                        $.extend(true, {}, checkoutProvider.get('billingAddress'), billingAddressData)
                    );
                }
                checkoutProvider.on('billingAddress', function (billingAddrsData) {
                    checkoutData.setBillingAddressFromData(billingAddrsData);
                });
            });
        },

        
        /**
         * Showing billing form when untick the billing checkbox.
         *
         * @returns {Boolean}
         */
        useShippingAddress: function () {

            this._super();

            if (!this.isAddressSameAsShipping()) {
                this.isAddressFormVisible(true);
            }

            return true;
        }

    };

    /**
     * AMD function call
     * @param {Object} target - Magento_Checkout/js/view/billing-address
     */
    return function (target) {
        return target.extend(mixin);
    };
});
