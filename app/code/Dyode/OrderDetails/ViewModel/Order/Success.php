<?php

namespace Dyode\OrderDetails\ViewModel\Order;

use Aheadworks\StoreLocator\Model\LocationFactory;
use Dyode\Checkout\Helper\CheckoutConfigHelper;
use Dyode\CheckoutDeliveryMethod\Model\DeliveryMethod;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Block\Product\ImageBuilder;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Data\ObjectFactory;
use Magento\Framework\DataObject;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

/**
 * Success View Model
 *
 */
class Success implements ArgumentInterface
{

    /**
     * @var \Magento\Sales\Model\Order\Address\Renderer
     */
    protected $addressRender;

    /**
     * @var \Magento\Sales\Api\Data\OrderInterface
     */
    protected $order;

    /**
     * @var \Dyode\OrderDetails\ViewModel\Order\LocationFactory
     */
    protected $locationFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Catalog\Block\Product\ImageBuilder
     */
    protected $imageBuilder;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var \Magento\Framework\Data\ObjectFactory
     */
    protected $dataObjectFactory;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    protected $jsonHelper;

    /**
     * @var \Dyode\Checkout\Helper\CheckoutConfigHelper
     */
    protected $checkoutConfigHelper;


    /**
     * Success constructor.
     *
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRender
     * @param \Magento\Sales\Api\Data\OrderInterface $order
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Aheadworks\StoreLocator\Model\LocationFactory $locationFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Catalog\Block\Product\ImageBuilder $imageBuilder
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Framework\Data\ObjectFactory $dataObjectFactory
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     * @param \Dyode\Checkout\Helper\CheckoutConfigHelper $checkoutConfigHelper
     */
    public function __construct(
        AddressRenderer $addressRender,
        OrderInterface $order,
        ProductRepositoryInterface $productRepository,
        LocationFactory $locationFactory,
        CheckoutSession $checkoutSession,
        ImageBuilder $imageBuilder,
        PriceHelper $priceHelper,
        ObjectFactory $dataObjectFactory,
        Json $jsonHelper,
        CheckoutConfigHelper $checkoutConfigHelper
    ) {
        $this->addressRender = $addressRender;
        $this->order = $order;
        $this->productRepository = $productRepository;
        $this->locationFactory = $locationFactory;
        $this->checkoutSession = $checkoutSession;
        $this->imageBuilder = $imageBuilder;
        $this->priceHelper = $priceHelper;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->jsonHelper = $jsonHelper;
        $this->checkoutConfigHelper = $checkoutConfigHelper;
    }

    /**
     * Get the last order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->checkoutSession->getLastRealOrder();
    }

    /**
     * Get shipping address HTML
     *
     * @return string
     */
    public function shippingAddressHtml()
    {
        return $this->getFormattedAddress($this->getOrder()->getShippingAddress());
    }

    /**
     * Get billing address HTML
     *
     * @return string
     */
    public function billingAddressHtml()
    {
        return $this->getFormattedAddress($this->getOrder()->getBillingAddress());
    }

    /**
     * Provide payment method title.
     *
     * @return string
     */
    public function paymentMethodTitle()
    {
        $payment = $this->getOrder()->getPayment();
        $method = $payment->getMethodInstance();

        return $method->getTitle();
    }

    /**
     * Get the store location entry based on the location code.
     *
     * @param int $locId
     * @return \Aheadworks\StoreLocator\Model\Location
     */
    public function getPickupLocation($locId)
    {
        return $this->locationFactory->create()->load($locId, 'store_location_code');
    }

    /**
     * Prepare last order item details as per the success page needed.
     *
     * @return array $items
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function orderItems()
    {
        $items = [];

        /** @var \Magento\Sales\Api\Data\OrderItemInterface $item */
        foreach ($this->getOrder()->getAllVisibleItems() as $item) {
            if ($item->getIsVirtual()) {
                continue;
            }

            $storeInfo = false;
            $shippingInfo = false;
            $deliveryType = $item->getDeliveryType();
            $product = $this->productRepository->getById($item->getProductId());
            $productImage = $this->getImage($product, 'category_page_list');

            if ($this->isShipToHome($deliveryType)) {
                $shippingInfo = new DataObject($this->jsonHelper->unserialize($item->getShippingDetails()));
            }

            if ($this->isStorePickup($deliveryType)) {
                $storeInfo = $this->getPickupLocation($item->getPickupLocation());
            }

            $orderItemData = new DataObject([
                'product_image_url'  => $product->getProductUrl(),
                'product_image_html' => $productImage->toHtml(),
                'name'               => $item->getName(),
                'formatted_price'    => $this->priceHelper->currency($item->getPrice(), true, false),
                'quantity_ordered'   => intval($item->getQtyOrdered()),
                'delivery_type'      => $deliveryType,
                'store_info'         => $storeInfo,
                'shipping_info'      => $shippingInfo,
                'shipping_message'   => $this->getDeliveryMessage($shippingInfo),

            ]);

            $items[] = $orderItemData;
        }

        return $items;
    }

    /**
     * Checks whether the delivery type corresponds to the order item is store_pickup.
     *
     * @param int|string $deliveryType
     * @return bool
     */
    public function isStorePickup($deliveryType)
    {
        if ($deliveryType == DeliveryMethod::DELIVERY_OPTION_STORE_PICKUP_ID) {
            return true;
        }

        return false;
    }

    /**
     * Checks whether the delivery type corresponds to the order item is ship_to_home.
     *
     * @param int|string $deliveryType
     * @return bool
     */
    public function isShipToHome($deliveryType)
    {
        if ($deliveryType == DeliveryMethod::DELIVERY_OPTION_SHIP_TO_HOME_ID) {
            return true;
        }

        return false;
    }

    /**
     * Retrieve product image
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $imageId
     * @param array $attributes
     * @return \Magento\Catalog\Block\Product\Image
     */
    protected function getImage($product, $imageId, $attributes = [])
    {
        return $this->imageBuilder->setProduct($product)
            ->setImageId($imageId)
            ->setAttributes($attributes)
            ->create();
    }

    /**
     * Get shipping message based on the shipping information.
     *
     * @param DataObject|bool $shippingInfo
     * @return string
     */
    protected function getDeliveryMessage($shippingInfo)
    {
        if (!$shippingInfo) {
            return '';
        }

        $message = '';
        $methodCode = $shippingInfo->getMethodCode();
        $carrierCode = $shippingInfo->getCarrierCode();
        $carrierMsg = $this->checkoutConfigHelper->collectShippingMethodDeliveryMsgs();

        if (isset($carrierMsg[$carrierCode])) {
            $message = $carrierMsg[$carrierCode];
        }

        if ($carrierCode == 'ups' && $methodCode == '2DA') {
            $message = date('M d,Y', strtotime("+2 days"));
        }

        if ($carrierCode == 'ups' && $methodCode == '3DS') {
            $message = date('M d,Y', strtotime("+3 days"));
        }

        return $message;
    }

    /**
     * Get the address html
     *
     * @param $address
     * @return mixed
     */
    protected function getFormattedAddress($address)
    {
        return $this->addressRender->format($address, 'html');
    }
}