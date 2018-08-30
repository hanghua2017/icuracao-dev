<?php

namespace Dyode\PromotionWidget\Model\Config\Source;

class Select implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'single', 'label' => __('Single')],
            ['value' => 'double', 'label' => __('Double')],
            ['value' => 'triple', 'label' => __('Triple')],
            ['value' => 'quadruple', 'label' => __('Quadruple')]
        ];
    }
}
