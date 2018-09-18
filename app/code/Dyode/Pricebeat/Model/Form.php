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
 *                     @category  Dyode
 *                     @package   Dyode_Pricebeat
 *                     @copyright Copyright (c) 2017
 *                     @license   http://opensource.org/licenses/mit-license.php MIT License
 */
namespace Dyode\Pricebeat\Model;

/**
 * @method Form setTitle($title)
 * @method Form setFormfile($formfile)
 * @method Form setFormthumbnail($formthumbnail)
 * @method Form setStatus($status)
 * @method mixed getTitle()
 * @method mixed getFormfile()
 * @method mixed getFormthumbnail()
 * @method mixed getStatus()
 * @method Form setCreatedAt(\string $createdAt)
 * @method string getCreatedAt()
 * @method Form setUpdatedAt(\string $updatedAt)
 * @method string getUpdatedAt()
 */
class Form extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'dyode_pricebeat_form';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'dyode_pricebeat_form';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'dyode_pricebeat_form';


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Dyode\Pricebeat\Model\ResourceModel\Form');
    }

    /**
     * Get identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * get entity default values
     *
     * @return array
     */
    public function getDefaultValues()
    {
        $values = [];

        return $values;
    }
}
