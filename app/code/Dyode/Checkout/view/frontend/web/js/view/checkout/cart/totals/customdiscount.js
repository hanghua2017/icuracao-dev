define(
    [
        'Dyode_Checkout/js/view/checkout/summary/customdiscount'
    ],
    function (Component) {
        'use strict';

        return Component.extend({

            /**
             * @override
             */
            isDisplayed: function () {
                return true;
            }
        });
    }
);
