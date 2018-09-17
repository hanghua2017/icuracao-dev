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
namespace Dyode\Pricebeat\Model\ResourceModel;

class Form extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Date model
     *
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * constructor
     *
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     */
    public function __construct(
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Model\ResourceModel\Db\Context $context
    )
    {
        $this->date = $date;
        parent::__construct($context);
    }


    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('dyode_pricebeat_form', 'form_id');
    }

    /**
     * Retrieves Video Title from DB by passed id.
     *
     * @param string $id
     * @return string|bool
     */
    public function getVideoTitleById($id)
    {
        $adapter = $this->getConnection();
        $select = $adapter->select()
            ->from($this->getMainTable(), 'title')
            ->where('form_id = :form_id');
        $binds = ['form_id' => (int)$id];
        return $adapter->fetchOne($select, $binds);
    }

    /**
     * Retrieves Video thumbnail path and file path from DB by passed id whose status is enabled.
     *
     * @param string $id
     * @return string|bool
     */
    public function getVideoDataById($id)
    {
        $adapter = $this->getConnection();
        $fields = array('first_name','last_name','email','phonenumber','account_number','invoice_number','product_url',  'product_image_url','imagethumbnail','redirect_url');
        $select = $adapter->select()
            ->from($this->getMainTable(),$fields)
            ->where('form_id = :form_id');
        $binds = ['form_id' => (int)$id];
        return $adapter->fetchAll($select, $binds);
    }

    /**
     * Retrieves Video Options i.e. video_id & title from DB by passed id whose status is enabled.
     *
     * @return string|bool
     */
    public function getVideoOptions()
    {
        $adapter = $this->getConnection();
        $fields = array('form_id', 'title');
        $select = $adapter->select()
            ->from($this->getMainTable(),$fields)
            ->where('status = true');
        return $adapter->fetchAll($select);
    }

    /**
     * Retrieves Video Options i.e. video_id & title from DB by passed id whose status is enabled.
     *
     * @return string|bool
     */
    public function getAllVideos()
    {
        $adapter = $this->getConnection();
        $fields = array('form_id','first_name','last_name','email','phonenumber','account_number','invoice_number','product_url', 'redirect_url','show_in_frontend','imagethumbnail','product_image_url');
        $select = $adapter->select()
            ->from($this->getMainTable(),$fields)
            ->where('status = true');
        return $adapter->fetchAll($select);
    }

    /**
     * before save callback
     *
     * @param \Magento\Framework\Model\AbstractModel|\Dyode\Pricebeat\Model\Video $object
     * @return $this
     */
    protected function _beforeSave(\Magento\Framework\Model\AbstractModel $object)
    {
        $object->setUpdatedAt($this->date->date());
        if ($object->isObjectNew()) {
            $object->setCreatedAt($this->date->date());
        }
        return parent::_beforeSave($object);
    }
}
