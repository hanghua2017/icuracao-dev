<?php
/**
 * Dyode_Pricebeat extension
 *                     NOTICE OF LICENSE
 *
 *                     This source file is subject to the MIT License
 *                     that is bundled with this package in the file LICENSE.txt.
 *                     It is also available through the world-wide-web at this URL:
 *                     http://opensource.org/licenses/mit-license.php
 *
 *                     @category  Pricebeat
 *                     @package   Dyode_Pricebeat
 *                     @copyright Copyright (c) 2017
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Dyode\Pricebeat\Model\ResourceModel\Form;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * ID Field Name
     *
     * @var string
     */
    protected $_idFieldName = 'form_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'dyode_pricebeat_form_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'form_collection';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dyode\Pricebeat\Model\Form', 'Dyode\Pricebeat\Model\ResourceModel\Form');
    }

    /**
     * Get SQL for get record count.
     * Extra GROUP BY strip added.
     *
     * @return \Magento\Framework\DB\Select
     */
    public function getSelectCountSql()
    {
        $countSelect = parent::getSelectCountSql();
        $countSelect->reset(\Zend_Db_Select::GROUP);
        return $countSelect;
    }
    /**
     * @param string $valueField
     * @param string $labelField
     * @param array $additional
     * @return array
     */
    protected function _toOptionArray($valueField = 'form_id', $labelField = 'Form ID', $additional = [])
    {
        return parent::_toOptionArray($valueField, $labelField, $additional);
    }
}
