<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block\Adminhtml\Location;

use Aheadworks\StoreLocator\Api\LocationRepositoryInterface;
use Aheadworks\StoreLocator\Controller\RegistryConstants;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Registry;

/**
 * Class Edit.
 */
class Edit extends Container
{
    /**
     * @var Registry
     */
    protected $coreRegistry = null;

    /**
     * @var LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param LocationRepositoryInterface $locationRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        LocationRepositoryInterface $locationRepository,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        $this->locationRepository = $locationRepository;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_objectId = 'location_id';
        $this->_blockGroup = 'Aheadworks_StoreLocator';
        $this->_controller = 'adminhtml_location';

        parent::_construct();

        if ($this->isAllowed('Aheadworks_StoreLocator::location_save')) {
            $this->buttonList->update('save', 'label', __('Save Location'));
            $this->buttonList->add(
                'saveandcontinue',
                [
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => [
                        'mage-init' => [
                            'button' => ['event' => 'saveAndContinueEdit', 'target' => '#edit_form'],
                        ],
                    ]
                ],
                -100
            );
        } else {
            $this->buttonList->remove('save');
        }

        if ($this->isAllowed('Aheadworks_StoreLocator::location_delete')) {
            $this->buttonList->update('delete', 'label', __('Delete Location'));
        } else {
            $this->buttonList->remove('delete');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getHeaderText()
    {
        $locationId = $this->coreRegistry->registry(RegistryConstants::CURRENT_LOCATION_ID);

        if ($locationId) {
            $location = $this->locationRepository->getById($locationId);
            $title = $this->escapeHtml($location->getTitle());
        } else {
            $title =  __('New Location');
        }

        return $title;
    }

    /**
     * @param string $resourceId
     * @return bool
     */
    protected function isAllowed($resourceId)
    {
        return $this->_authorization->isAllowed($resourceId);
    }

    /**
     * @return string
     */
    public function _getSaveAndContinueUrl()
    {
        return $this->getUrl(
            'aw_store_locator/location/save',
            ['_current' => true, 'back' => 'edit', 'active_tab' => '{{tab_id}}']
        );
    }

    /**
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('aw_store_locator/location/validate', ['_current' => true]);
    }
}
