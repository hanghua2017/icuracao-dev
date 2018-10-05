define(['ko'], function (ko) {
    var deliveryOptions = window.checkoutConfig.deliveryOptions,
        deliveryData = ko.observableArray(deliveryOptions || []);

    return {
        deliveryData: deliveryData,

        /**
         * Provide current delivery option info
         *
         * @returns {Array}
         */
        getDeliveryData: function () {
            return deliveryData();
        }
    }
});
