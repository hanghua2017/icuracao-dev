var config = {
    'config': {
        'mixins': {
            'Dyode_ShippingStep/js/view/shipping-method-step': {
                'Dyode_DeliveryMethod/js/view/checkout-steps-mixin': true
            },
            'Dyode_Checkout/js/view/shipping': {
                'Dyode_DeliveryMethod/js/view/checkout-steps-mixin': true
            },
            'Magento_Checkout/js/view/payment': {
                'Dyode_DeliveryMethod/js/view/checkout-steps-mixin': true
            }
        }
    }
}