<?php
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

namespace Dyode\Catalog\Block\Product\View\Options\Type;


use Magento\Catalog\Api\Data\ProductCustomOptionInterface;

/**
 * Select Preference Class
 *
 * Added in order to incorporate warranty and wall mount custom options.
 *
 */
class SelectPreference extends \Magento\Catalog\Block\Product\View\Options\Type\Select
{

    /**
     * @inheritdoc
     */
    public function getValuesHtml()
    {
        $_option = $this->getOption();
        $isWallMountOption = (bool)($_option->getIsWallMount() == 1);
        $isWarrantyOption = (bool)($_option->getIsWarranty() == 1);
        $isDropDownOption = (bool)($_option->getType() == ProductCustomOptionInterface::OPTION_TYPE_DROP_DOWN);
        $isMultipleOption = (bool)($_option->getType() == ProductCustomOptionInterface::OPTION_TYPE_MULTIPLE);
        if (!($isWarrantyOption || $isWallMountOption) || $isDropDownOption || $isMultipleOption) {
            return parent::getValuesHtml();
        }

        $configValue = $this->getProduct()
            ->getPreconfiguredValues()
            ->getData('options/' . $_option->getId());
        $store = $this->getProduct()->getStore();
        $this->setSkipJsReloadPrice(1);

        $selectHtml = '<div class="options-list nested" id="options-' . $_option->getId() . '-list">';
        $require = $_option->getIsRequire() ? ' required' : '';
        $arraySign = '';
        switch ($_option->getType()) {
            case ProductCustomOptionInterface::OPTION_TYPE_RADIO:
                $type = 'radio';
                $class = 'radio admin__control-radio';
                break;
            case ProductCustomOptionInterface::OPTION_TYPE_CHECKBOX:
                $type = 'checkbox';
                $class = 'checkbox admin__control-checkbox';
                $arraySign = '[]';
                break;
        }
        $count = 1;
        foreach ($_option->getValues() as $_value) {
            $count++;

            $priceStr = $this->_formatPrice(
                [
                    'is_percent'    => $_value->getPriceType() == 'percent',
                    'pricing_value' => $_value->getPrice($_value->getPriceType() == 'percent'),
                ]
            );

            $htmlValue = $_value->getOptionTypeId();
            if ($arraySign) {
                $checked = is_array($configValue) && in_array($htmlValue, $configValue) ? 'checked' : '';
            } else {
                $checked = $configValue == $htmlValue ? 'checked' : '';
            }

            $dataSelector = 'options[' . $_option->getId() . ']';
            if ($arraySign) {
                $dataSelector .= '[' . $htmlValue . ']';
            }

            $selectHtml .= '<div class="field choice admin__field admin__field-option' .
                $require .
                '">' .
                '<input type="' .
                $type .
                '" class="' .
                $class .
                ' ' .
                $require .
                ' product-custom-option"' .
                ($this->getSkipJsReloadPrice() ? '' : ' onclick="opConfig.reloadPrice()"') .
                ' name="options[' .
                $_option->getId() .
                ']' .
                $arraySign .
                '" id="options_' .
                $_option->getId() .
                '_' .
                $count .
                '" value="' .
                $htmlValue .
                '" ' .
                $checked .
                ' data-selector="' . $dataSelector . '"' .
                ' price="' .
                $this->pricingHelper->currencyByStore($_value->getPrice(true), $store, false) .
                '" />' .
                '<label class="label admin__field-label" for="options_' .
                $_option->getId() .
                '_' .
                $count .
                '"><span>' .
                $_value->getTitle() .
                '</span> ' .
                $priceStr .
                '</label>';
            $selectHtml .= '</div>';
        }
        $selectHtml .= '</div>';

        return $selectHtml;

    }
}