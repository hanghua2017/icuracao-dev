var config = {
 'config': {
     'mixins': {
        'Dyode_Checkout/js/view/shipping': {
           'Dyode_DeliveryMethod/js/view/shipping-payment-mixin': true
           },
        'Magento_Checkout/js/view/payment': {
            'Dyode_DeliveryMethod/js/view/shipping-payment-mixin': true
           }
    }
 }
}
