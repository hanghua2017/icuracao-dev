<?php

/**
 * Dyode
 *
 * @category  Dyode
 * @package   Dyode_Rma
 * @author    Sreya
 */
namespace Dyode\Rma\Service\Rma\RmaManagement;

class Save implements \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SaveInterface
{
    public function __construct(
        \Mirasvit\Rma\Api\Repository\MessageRepositoryInterface $messageRepository,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagement\SearchInterface $rmaSearchManagement,
        \Mirasvit\Rma\Api\Service\Message\MessageManagement\AddInterface $messageAddService,
        \Mirasvit\Rma\Api\Service\Rma\RmaManagementInterface $rmaManagement,
        \Mirasvit\Rma\Model\RmaFactory $rmaFactory,
        \Mirasvit\Rma\Service\Item\Update $itemUpdateService,
        \Mirasvit\Rma\Service\Order\OrderAbstractFactory $orderAbstractFactory,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Registry $registry
    ) {
        $this->messageRepository      = $messageRepository;
        $this->rmaSearchManagement    = $rmaSearchManagement;
        $this->itemUpdateService      = $itemUpdateService;
        $this->messageAddService      = $messageAddService;
        $this->rmaManagement          = $rmaManagement;
        $this->registry               = $registry;
        $this->rmaFactory             = $rmaFactory;
        $this->eventManager           = $eventManager;
        $this->orderAbstractFactory   = $orderAbstractFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function saveRma($performer, $data, $items)
    {
        $rma = $this->rmaFactory->create();
        if (isset($data['rma_id']) && $data['rma_id']) {
            $rma->load($data['rma_id']);
        }
        unset($data['rma_id']);

        $rma = $this->updateRma($performer, $rma, $data);

        $this->itemUpdateService->updateItems($rma, $items);

        $this->eventManager->dispatch('rma_update_rma_after', ['rma' => $rma, 'performer' => $performer]);

        if (
            (isset($data['reply']) && $data['reply'] != '') ||
            (!empty($_FILES['attachment']) && !empty($_FILES['attachment']['name'][0]))
        ) {
            $this->messageAddService->addMessage($performer, $rma, $data['reply'], $data);
        }

        return $rma;
    }

    /**
     * @param \Mirasvit\Rma\Api\Service\Performer\PerformerInterface $performer
     * @param \Mirasvit\Rma\Api\Data\RmaInterface                    $rma
     * @param array                                                  $data
     *
     * @return \Mirasvit\Rma\Api\Data\RmaInterface
     */
    protected function updateRma($performer, $rma, $data)
    {
        if (isset($data['street2']) && $data['street2'] != '') {
            $data['street'] .= "\n".$data['street2'];
            unset($data['street2']);
        }

        $this->rma = $rma;
        $order = $this->orderAbstractFactory->get($data);
        $order->getResource()->load($order, $data['order_id']);

        $this->rma->addData($data);
        $this->rma->setIfOffline($order->getIsOffline());

        $storeId = $order->getStoreId();
        if (!$storeId && isset($data['store_id'])) {
            $storeId = (int)$data['store_id'];
        }
        $this->rma->setCustomerId($order->getCustomerId());
        $this->rma->setStoreId($storeId);

        if (!$order->getCustomerId() && empty($this->rma->getEmail())) {
            $this->setRmaCustomerInfo($this->rma, $performer);
        } else {
            $this->setRmaAddress($this->rma, $order);
        }

        $performer->setRmaAttributesBeforeSave($this->rma);

        //setting customer name from shipping address for rma guest
        if (empty($this->rma->getFirstname())) {
            $this->rma->setFirstname($order->getShippingAddress()->getFirstname());
            if (empty($this->rma->getLastname()) && (!empty($order->getShippingAddress()->getLastname()))) {
                $this->rma->setLastname($order->getShippingAddress()->getLastname());
            }
        }

        $this->rma->save();

        $this->registry->register('current_rma', $this->rma);

        return $this->rma;
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @return void
     */
    protected function setRmaAddress($rma)
    {
        $customer = $this->rmaManagement->getCustomer($rma);
        $address = $customer->getDefaultBillingAddress();
        if ($address) {
            $this->setRmaAddressData($rma, $address);
        } else {
            $this->setRmaCustomerInfo($rma, $customer);
        }
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @param \Magento\Customer\Model\Address     $address
     * @return void
     */
    public function setRmaAddressData($rma, $address)
    {
        $this->rma
            ->setFirstname($address->getFirstname())
            ->setLastname($address->getLastname())
            ->setCompany($address->getCompany())
            ->setTelephone($address->getTelephone())
            ->setStreet(implode("\n", $address->getStreet()))
            ->setCity($address->getCity())
            ->setCountryId($address->getCountryId())
            ->setRegionId($address->getRegionId())
            ->setRegion($address->getRegion())
            ->setPostcode($address->getPostcode());
    }

    /**
     * @param \Mirasvit\Rma\Api\Data\RmaInterface $rma
     * @param \Magento\Customer\Model\Customer    $customer
     * @return void
     */
    public function setRmaCustomerInfo($rma, $customer)
    {
        if (!method_exists($customer, 'getEmail') || !$customer->getEmail()) {
            return;
        }
        $this->rma
            ->setFirstname($customer->getFirstname())
            ->setLastname($customer->getLastname())
            ->setEmail($customer->getEmail())
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function markAsRead(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        /** @var \Mirasvit\Rma\Api\Data\MessageInterface $message */
        foreach ($this->rmaSearchManagement->getUnread($rma) as $message) {
            $message->setIsRead(true);
            $this->messageRepository->save($message);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function markAsUnread(\Mirasvit\Rma\Api\Data\RmaInterface $rma)
    {
        /** @var \Mirasvit\Rma\Api\Data\MessageInterface $message */
        foreach ($this->rmaSearchManagement->getRead($rma) as $message) {
            $message->setIsRead(false);
            $this->messageRepository->save($message);
        }
    }
}