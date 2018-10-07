var config = {
    'config': {
        'mixins': {
            'Dyode_CheckoutAddressStep/js/view/shipping-method-step': {
                'Dyode_CheckoutDeliveryMethod/js/view/checkout-steps-mixin': true
            },
            'Dyode_Checkout/js/view/shipping': {
                'Dyode_CheckoutDeliveryMethod/js/view/checkout-steps-mixin': true
            },
            'Magento_Checkout/js/view/payment': {
                'Dyode_CheckoutDeliveryMethod/js/view/checkout-steps-mixin': true
            }
        }
    }
}