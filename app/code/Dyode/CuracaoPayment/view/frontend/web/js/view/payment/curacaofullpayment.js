/**
 * Dyode_CuracaoPayment Magento2 Module.
 *
 * Provide the facility: curacao custom payment method
 *
 * @module    Dyode_CuracaoPayment
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

'use strict';

define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {

    rendererList.push({
        type: 'curacaofullpayment',
        component: 'Dyode_CuracaoPayment/js/view/payment/method-renderer/curacaofullpayment-method'
    });

    return Component.extend({});
});
