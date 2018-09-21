<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package   Dyode
 * @module    Dyode_Catalog
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Catalog\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * CustomOption Modifier Class
 *
 * Use to update custom options facility in catalog > product > edit section
 *
 * @package Dyode\Catalog\Ui\DataProvider\Product\Form\Modifier
 */
class CustomOptions implements ModifierInterface
{
    const MOUNT_FIELD = 'is_wall_mount';

    protected $meta;

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->addAdditionalModifications();
        return $this->meta;
    }

    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * Add warranty and wall-mount checkboxes into custom options ui-component.
     *
     * @return $this
     */
    public function addAdditionalModifications()
    {
        $this->meta["custom_options"]["children"]["options"]["children"]["record"]["children"]["container_option"]
        ["children"]["container_common"]["children"][static::MOUNT_FIELD] = $this->getYesNoFieldConfig(
            'Wall Mount', static::MOUNT_FIELD, 60
        );

        return $this;
    }

    /**
     * Defines a checkbox configuration.
     *
     * @param string $label     Checkbox label
     * @param string $dataScope Checkbox scope
     * @param int    $sortOrder Sort order
     * @return array
     */
    protected function getYesNoFieldConfig($label, $dataScope, $sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label'         => __($label),
                        'componentType' => Field::NAME,
                        'formElement'   => Checkbox::NAME,
                        'dataScope'     => $dataScope,
                        'dataType'      => Text::NAME,
                        'sortOrder'     => $sortOrder,
                        'value'         => '0',
                        'valueMap'      => [
                            'true'  => '1',
                            'false' => '0',
                        ],
                    ],
                ],
            ],
        ];
    }
}