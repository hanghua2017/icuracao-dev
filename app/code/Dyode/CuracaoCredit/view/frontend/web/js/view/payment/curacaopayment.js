define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'curacaopayment',
                component: 'Dyode_CuracaoCredit/js/view/payment/method-renderer/curacaopayment-method'
            }
        );
        return Component.extend({});
    }
);