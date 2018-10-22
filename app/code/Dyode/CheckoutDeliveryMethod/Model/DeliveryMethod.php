<?php
namespace Dyode\CheckoutDeliveryMethod\Model;

use Aheadworks\StoreLocator\Model\LocationFactory;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObjectFactory;
use Magento\Quote\Api\CartRepositoryInterface;
use Magento\Quote\Model\Quote;

class DeliveryMethod
{
    const DELIVERY_OPTION_SHIP_TO_HOME_CODE = 'ship_to_home';
    const DELIVERY_OPTION_STORE_PICKUP_CODE = 'store_pickup';
    const DELIVERY_OPTION_SHIP_TO_HOME_ID = 1;
    const DELIVERY_OPTION_STORE_PICKUP_ID = 2;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $deliveryQuote;

    /**
     * @var \Magento\Framework\DataObject
     */
    protected $deliveryInfo;

    /**
     * @var \Magento\Framework\Data\Collection
     */
    protected $deliveryInfoCollection;

    /**
     * @var \Magento\Framework\DataObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $quoteRepository;

    /**
     * @var \Aheadworks\StoreLocator\Model\LocationFactory
     */
    protected $locationFactory;

    /**
     * DeliveryMethod constructor.
     *
     * @param \Magento\Quote\Api\CartRepositoryInterface $quoteRepository
     * @param \Aheadworks\StoreLocator\Model\LocationFactory $locationFactory
     * @param \Magento\Framework\Data\Collection $deliveryInfoCollection
     * @param \Magento\Framework\DataObjectFactory $dataObjectFactory
     */
    public function __construct(
        CartRepositoryInterface $quoteRepository,
        LocationFactory $locationFactory,
        Collection $deliveryInfoCollection,
        DataObjectFactory $dataObjectFactory
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->deliveryInfoCollection = $deliveryInfoCollection;
        $this->locationFactory = $locationFactory;
        $this->dataObjectFactory = $dataObjectFactory;
    }

    /**
     * Save delivery info into the quote details.
     *
     * @param mixed $quote
     * @param array $deliveryInfo
     * @throws \Exception
     */
    public function save($quote, array $deliveryInfo)
    {
        $this->initiateQuote($quote);
        $this->prepareDeliveryInfo($deliveryInfo);

        try {
            $this->updateQuoteItems()->saveQuote();
        } catch (\Exception $exception) {
            throw new \Exception('Delivery information is not updated.');
        }
    }

    /**
     * Provide delivery methods available details in code => id format.
     *
     * @return array
     */
    public function deliveryCodeIdRelations()
    {
        return [
            self::DELIVERY_OPTION_SHIP_TO_HOME_CODE => self::DELIVERY_OPTION_SHIP_TO_HOME_ID,
            self::DELIVERY_OPTION_STORE_PICKUP_CODE => self::DELIVERY_OPTION_STORE_PICKUP_ID,
        ];
    }

    /**
     * Provide delivery methods available details in id => code format.
     *
     * @return array
     */
    public static function deliveryIdCodeRelations()
    {
        return [
            self::DELIVERY_OPTION_SHIP_TO_HOME_ID => self::DELIVERY_OPTION_SHIP_TO_HOME_CODE,
            self::DELIVERY_OPTION_STORE_PICKUP_ID => self::DELIVERY_OPTION_STORE_PICKUP_CODE,
        ];
    }

    /**
     * Update quote items with delivery information
     *
     * @return $this
     */
    public function updateQuoteItems()
    {
        foreach ($this->deliveryInfoCollection as $deliveryInfo) {
            //make sure delivery type passed is valid.
            $deliveryRelation = $this->deliveryCodeIdRelations();
            if (!isset($deliveryRelation[$deliveryInfo->getData('deliveryType')])) {
                continue;
            }

            //make sure quote item exist.
            $deliveryType = $deliveryRelation[$deliveryInfo->getData('deliveryType')];
            $quoteItem = $this->deliveryQuote->getItemById($deliveryInfo->getData('quoteItemId'));
            if (!$quoteItem) {
                continue;
            }

            //update store pickup info
            if ($deliveryType === self::DELIVERY_OPTION_STORE_PICKUP_ID && $deliveryInfo->getData('storeId')) {
                $pickupLocation = $this->getStore((int)$deliveryInfo->getData('storeId'));
                if ($pickupLocation) {
                    $quoteItem->setPickupLocation($pickupLocation->getStoreLocationCode());
                }
            }

            //update delivery related info
            $quoteItem->setDeliveryType($deliveryType);
        }

        return $this;
    }

    /**
     * Perform quote save.
     *
     * @throws \Exception
     */
    public function saveQuote()
    {
        $this->deliveryQuote->save();
    }

    /**
     * Collect store location information.
     *
     * @param $storeId
     * @return mixed|\Aheadworks\StoreLocator\Model\Location|null
     */
    protected function getStore($storeId)
    {
        return $this->locationFactory->create()->load($storeId);
    }

    /**
     * Initiate sales quote.
     *
     * @param $quote
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function initiateQuote($quote)
    {
        if ($quote instanceof Quote) {
            $this->deliveryQuote = $quote;
        }
        if (is_string($quote) || is_int($quote)) {
            $this->deliveryQuote = $this->quoteRepository->get((int)$quote);
        }

        return $this;
    }

    /**
     * Prepare delivery info collection
     *
     * @param array $deliveryInfo
     * @return $this
     * @throws \Exception
     */
    protected function prepareDeliveryInfo(array $deliveryInfo)
    {
        foreach ($deliveryInfo as $info) {
            $this->deliveryInfoCollection->addItem($this->newDeliveryInfoInstance()->setData($info));
        }

        return $this;
    }

    /**
     * New DataObject instance.
     *
     * @return \Magento\Framework\DataObject
     */
    protected function newDeliveryInfoInstance()
    {
        return $this->dataObjectFactory->create();
    }
}
