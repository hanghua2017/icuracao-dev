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

    protected $warrantyProduct;

    /**
     * AddWarrantyToCart constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Checkout\Model\CartFactory             $cartFactory
     * @param \Magento\Checkout\Model\Session                 $checkoutSession
     * @param \Magento\Catalog\Model\Product                 $warrantyProduct
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        CartFactory $cartFactory,
        Session $checkoutSession,
        Product $warrantyProduct
    ) {
        $this->productRepository = $productRepository;
        $this->cartFactory = $cartFactory;
        $this->checkoutSession = $checkoutSession;
        $this->warrantyProduct = $warrantyProduct;
    }

    /**
     * Add warranty to the cart if the warranty is selected.
     *
     * @inheritdoc
     */
    public function execute(Observer $observer)
    {
        $request = $observer->getRequest();

        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $logger = $objectManager->get("Psr\Log\LoggerInterface");
        $logger->info("addWarrantyIntoCart ++++". json_encode($request));

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
           // $product = $this->initProduct((int)$warranty);
           $product = $this->warrantyProduct->load((int)$warranty);
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
                            return false;
                        }
                    }
                
                }
            }
            $params = ['product' => $product->getId(),'qty' => $this->product->getQty()];
            $warrantyPrice = $product->getPrice();
            $logger->info("addWarrantyIntoCart ++++". $this->cart->getId()."  ".$product->getPrice());

            try {
                $product->setPrice($warrantyPrice);
                $this->cart->addProduct($product, $params);
               
                $this->cart->save();

               
                
            
            // $quote->addProduct($product->getId(), 1);
            // $quote->save(); //Now Save quote and your quote is ready
 
            // // Collect Totals
            // $quote->collectTotals();

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
        $quote = $this->checkoutSession->getQuote();

        if ($parentQuoteItem) {
            $this->getQuote()
                ->getItemByProduct($product)
                ->setPrice($product->getPrice())
                ->setRowTotal($product->getPrice())
                ->setWarrantyParentItemId($parentQuoteItem->getItemId())
                ->save();

                $warrantyPrice = $product->getPrice();
                $grand_total = $quote->getGrandTotal();
                $new_grand_total = $grand_total + $warrantyPrice;
                $this->checkoutSession->getQuote()
                    ->setGrandTotal($new_grand_total)
                    ->setBaseGrandTotal($new_grand_total)
                    ->setBaseSubtotal($new_grand_total)
                    ->setSubtotal($new_grand_total)
                    ->save();

                $this->checkoutSession->getQuote()->collectTotals()->save();
                
           // $this->cart->getQuote()->getItemById($product->getId())->setQty($qty);

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