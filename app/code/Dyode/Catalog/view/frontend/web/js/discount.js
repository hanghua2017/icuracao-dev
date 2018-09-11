/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package Dyode
 * @module  Dyode_Catalog
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

define([
    'jquery',
    'underscore',
], function($, _){
    return function(){
        $.widget('mage.SwatchRenderer', $['mage']['SwatchRenderer'], {


            /**
             * SwatchRenderer is responsible for hide/show regular price when there is a special price
             * option does exists for a configurable product.
             * Hence we are injecting discount show/hide to this module by redefining it.
             *
             * @inheritDoc
             */
             _UpdatePrice: function () {
                var $widget = this,
                    $product = $widget.element.parents($widget.options.selectorProduct),
                    $productPrice = $product.find(this.options.selectorProductPrice),
                    options = _.object(_.keys($widget.optionsMap), {}),
                    result,
                    tierPriceHtml,
                    discountSelector = '.price-discount';

                $widget.element.find('.' + $widget.options.classes.attributeClass + '[option-selected]').each(function () {
                    var attributeId = $(this).attr('attribute-id');

                    options[attributeId] = $(this).attr('option-selected');
                });

                result = $widget.options.jsonConfig.optionPrices[_.findKey($widget.options.jsonConfig.index, options)];

                $productPrice.trigger(
                    'updatePrice',
                    {
                        'prices': $widget._getPrices(result, $productPrice.priceBox('option').prices)
                    }
                );

                if (typeof result != 'undefined' && result.oldPrice.amount !== result.finalPrice.amount) {
                    $(this.options.slyOldPriceSelector).show();

                    var discount = this.calculateDiscount(result.oldPrice.amount, result.finalPrice.amount),
                        discountHtml = discount + '% OFF';

                    $(discountSelector).html(discountHtml);
                    $(discountSelector).show();
                } else {
                    $(this.options.slyOldPriceSelector).hide();
                    $(discountSelector).hide();
                }

                if (typeof result != 'undefined' && result.tierPrices.length) {
                    if (this.options.tierPriceTemplate) {
                        tierPriceHtml = mageTemplate(
                            this.options.tierPriceTemplate,
                            {
                                'tierPrices': result.tierPrices,
                                '$t': $t,
                                'currencyFormat': this.options.jsonConfig.currencyFormat,
                                'priceUtils': priceUtils
                            }
                        );
                        $(this.options.tierPriceBlockSelector).html(tierPriceHtml).show();
                    }
                } else {
                    $(this.options.tierPriceBlockSelector).hide();
                }
            },


            calculateDiscount: function(oldPrice, finalPrice) {
                return (((oldPrice-finalPrice)/oldPrice)*100).toFixed();
            },

        });

        return $['mage']['SwatchRenderer'];
    };
});