<?php
/**
 * Dyode_Checkout Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package   Dyode
 * @module    Dyode_Catalog
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */

namespace Dyode\Catalog\Observer;

use Magento\Catalog\Model\Product;
use Magento\Checkout\Model\Session;
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
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * AddWarrantyToCart constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Checkout\Model\CartFactory             $cartFactory
     * @param \Magento\Checkout\Model\Session                 $checkoutSession
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CartFactory $cartFactory,
        Session $checkoutSession
    ) {
        $this->productRepository = $productRepository;
        $this->cartFactory = $cartFactory;
        $this->checkoutSession = $checkoutSession;
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
        $quote = $this->checkoutSession->getQuote();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->get("Psr\Log\LoggerInterface");
        $logger->info("addWarrantyIntoCart ++++". $quote->getId());

        foreach ($warranties as $warranty) {
            $product = $this->initProduct((int)$warranty);
            //Check if there is a quote
            if($quote){
                //Get all item from cart
                $allItems = $this->cart->getItems();
                $logger->info("addWarrantyIntoCart ++++". json_encode($allItems));
                foreach ($allItems as $eachItem) {
                    if($eachItem->getWarrantyParentItemId() != null){
                        $warrantyInCart = $eachItem->getProductId();
                        if($warrantyInCart ==  $product->getId()){
                            $logger->info("parentInCart == this->product->getId()". $product->getId());
                            throw new \Exception('Already warranty is added against this product');
                        }
                    }
                
                }
            }
            $params = ['qty' => $this->product->getQty()];

            try {
                $this->cart->addProduct($product, $params);
                $this->cart->save();

                $this->establishWarrantyQuoteRelation($product);

            } catch (\Exception $e) {
                return false;
            }
        }

        return $this;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }

    /**
     * Set a warranty quote item to the parent quote item.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    protected function establishWarrantyQuoteRelation(Product $product)
    {
        $parentQuoteItem = $this->getQuote()->getItemByProduct($this->product);

        if ($parentQuoteItem) {
            $this->getQuote()
                ->getItemByProduct($product)
                ->setWarrantyParentItemId($parentQuoteItem->getItemId())
                ->save();

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