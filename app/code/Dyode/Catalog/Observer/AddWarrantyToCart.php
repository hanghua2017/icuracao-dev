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

namespace Dyode\Catalog\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\CartFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * AddWarrantyToCart Observer
 */
class AddWarrantyToCart implements ObserverInterface
{

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Checkout\Model\CartFactory
     */
    protected $cartFactory;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * AddWarrantyToCart constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Checkout\Model\CartFactory             $cartFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CartFactory $cartFactory
    ) {
        $this->productRepository = $productRepository;
        $this->cartFactory = $cartFactory;
    }

    /**
     * Add warranty to the cart if the warranty is selected.
     *
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();
        $warrantyParam = $request->getParam('warranty');

        if (!$warrantyParam || !is_array($warrantyParam) || count($warrantyParam) === 0) {
            return $this;
        }

        $this->request = $request;
        $this->product = $observer->getProduct();
        $this->cart = $this->cartFactory->create();

        $this->addWarrantyIntoCart($warrantyParam);
        return $this;
    }

    /**
     * Add warranty to the cart if the warranty is selected.
     *
     * @param array $warranties
     * @return bool
     */
    public function addWarrantyIntoCart(array $warranties)
    {
        foreach ($warranties as $warranty) {
            $product = $this->initProduct((int)$warranty);
            $params = ['qty' => $this->product->getQty()];

            try {
                $this->cart->addProduct($product, $params);
                $this->cart->save();
            } catch (\Exception $e) {
                return false;
            }
        }

        return $this;
    }

    /**
     * Loads warranty product in order to add it into the cart.
     *
     * @param $productId
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     */
    protected function initProduct($productId)
    {
        try {
            return $this->productRepository->getById($productId, false, $this->product->getStoreId());
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}