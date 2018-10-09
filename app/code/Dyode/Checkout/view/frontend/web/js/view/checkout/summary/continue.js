define([
    'uiComponent',
    'Magento_Checkout/js/model/step-navigator',
], function(Component, stepNavigator) {
    return Component.extend({
        defaults: {
            template: 'Dyode_Checkout/checkout/summary/continue'
        },

        navigateToNextStep: function () {
            stepNavigator.next();
        },
    });
});