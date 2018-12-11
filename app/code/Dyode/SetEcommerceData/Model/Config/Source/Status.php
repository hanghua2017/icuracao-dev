<?php

namespace Dyode\SetEcommerceData\Model\Config\Source;

class Status implements \Magento\Framework\Option\ArrayInterface
{
 public function toOptionArray()
  {
    return [
      ['value' => 'restart', 'label' => __('Restart')],
      ['value' => 'running', 'label' => __('Running')],
      ['value' => 'stop', 'label' => __('Stop')]
    ];
  }
}