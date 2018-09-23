<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Controller\Adminhtml\Location;

use Aheadworks\StoreLocator\Model\ResourceModel\Location\Collection;
use Magento\Backend\App\Action\Context;
use Aheadworks\StoreLocator\Model\ResourceModel\Location\CollectionFactory;
use Aheadworks\StoreLocator\Api\LocationRepositoryInterface;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class MassDelete.
 */
class MassDelete extends AbstractMassAction
{
    /**
     * @var LocationRepositoryInterface
     */
    protected $locationRepository;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param LocationRepositoryInterface $locationRepositoryInterface
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        LocationRepositoryInterface $locationRepositoryInterface
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->locationRepository = $locationRepositoryInterface;
    }

    /**
     * @param Collection $collection
     * @return Redirect
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function massAction(Collection $collection)
    {
        $locationsDeleted = 0;

        foreach ($collection->getAllIds() as $locationId) {
            $this->locationRepository->deleteById($locationId);
            $locationsDeleted++;
        }

        if ($locationsDeleted) {
            $this->messageManager->addSuccess(__('A total of %1 location(s) were deleted.', $locationsDeleted));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }
}
