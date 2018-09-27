/**
 * Copyright © Dyode
 *
 */
define([
    'jquery',
    'jquery/ui'
], function ($) {
    'use strict';

    /**
     * This is used to automatically update the quantity of a product, when
     * it is changed in the qty input column.
     */
    $.widget('mage.updateCart', {

        /** @inheritdoc */
        _create: function () {
            var items = $.find('[data-role="cart-item-qty"]'),
                i,
                Item;

            //binding keyup event to the qty input fields.
            for (i = 0; i < items.length; i++) {
                Item = $(items[i]);
                Item.on('keyup', $.proxy(function () {
                    if (Item.val() > 0) {
                        $(this.options.updateCartBtn).trigger('click');
                    }
                }, this));
            }
        }
    });

    return $.mage.updateCart;
});