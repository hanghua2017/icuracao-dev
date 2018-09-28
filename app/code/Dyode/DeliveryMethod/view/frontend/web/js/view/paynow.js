/*global define*/
define([
    'jquery',
    'ko',
    'Magento_Ui/js/form/form',
  ], function(
      $,
      ko,
      Component
    ) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Dyode_DeliveryMethod/paynow'
        },
        initialize: function () {
            return this;
        },
    });
});
