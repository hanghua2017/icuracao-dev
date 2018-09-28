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
    'mage/translate',
    'priceUtils',

], function ($, _, $t, priceUtils) {
    return function () {
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
                    tierPriceHtml;

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
                    this.updateDiscountSection(result);

                } else {
                    $(this.options.slyOldPriceSelector).hide();
                    this.updateDiscountSection(false);
                }

                this.updateEmiSection(result);

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


            /**
             * Calculate discount price based on regular and final price.
             *
             * @param {float} oldPrice
             * @param {float} finalPrice
             * @returns {string}
             */
            calculateDiscount: function (oldPrice, finalPrice) {
                return (((oldPrice - finalPrice) / oldPrice) * 100).toFixed();
            },

            /**
             * Calculate curacao emi based on the price.
             *
             * @param float price
             * @returns {number}
             */
            calculateEmi: function (price) {
                if (price > 1000) {
                    return price * 0.05;
                }
                if (price > 500 && price <= 1000) {
                    return price * 0.075;
                }
                if (price > 200 && price <= 500) {
                    return price * 0.1;
                }
                if (price > 40 && price <= 200) {
                    return 20;
                }

                return 0;
            },

            /**
             * Show/hide discount section based on the result object.
             *
             * @param {object|boolean|undefined} result
             * @returns {mage.SwatchRenderer}
             */
            updateDiscountSection: function (result) {
                var discountSelector = '.price-discount';

                if (result === false) {
                    $(discountSelector).hide();
                    return this;
                }

                var discount = this.calculateDiscount(result.oldPrice.amount, result.finalPrice.amount),
                    discountHtml = discount + '% ' + $t('OFF');

                $(discountSelector).html(discountHtml);
                $(discountSelector).show();
            },

            /**
             * Show/hide curacao emi section based on the result object.
             *
             * @param {object|boolean|undefined}  result
             * @returns {mage.SwatchRenderer}
             */
            updateEmiSection: function (result) {
                var emiSelector = '.credit-card-emi',
                    emiRate = 0;

                if (typeof result == 'undefined') {
                    return this;
                }

                if (result === false) {
                    $(emiSelector).hide();
                    return this;
                }

                var oldPrice = parseFloat(result.oldPrice.amount),
                    finalPrice = parseFloat(result.finalPrice.amount);


                if (oldPrice > 0) {
                    emiRate = this.calculateEmi(oldPrice);
                }

                if (finalPrice > 0) {
                    emiRate = this.calculateEmi(finalPrice);
                }

                if (emiRate > 0) {

                    var emiHtml = '<span class="price">'+priceUtils.formatPrice(emiRate)+'</span>/'+$t('month');
                    $(emiSelector + ' .emi-amount').html(emiHtml);
                    $(emiSelector).show();
                } else {
                    $(emiSelector).hide();
                }

                return this;

            },

        });

        return $['mage']['SwatchRenderer'];
    };
});