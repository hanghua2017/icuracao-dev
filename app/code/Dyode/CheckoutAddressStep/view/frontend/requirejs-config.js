/**
 * Dyode_CheckoutAddressStep Magento2 Module.
 *
 * Mixin that provide custom behaviour to billing address.
 */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/billing-address': {
                'Dyode_CheckoutAddressStep/js/mixin/billing-address-mixin': true
            }
        }
    }
};
