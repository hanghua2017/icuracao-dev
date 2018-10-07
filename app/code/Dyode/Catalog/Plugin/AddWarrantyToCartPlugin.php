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
namespace Dyode\Catalog\Plugin;

use Magento\Catalog\Model\ProductRepository;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;

class AddWarrantyToCartPlugin
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Quote\Model\Quote
     */
    protected $quote;

    /**
     * @var \Magento\Quote\Model\Quote\Item
     */
    protected $parentQuoteItem;

    /**
     * @var \Magento\Catalog\Model\ProductRepository
     */
    protected $productRepository;

    /**
     * AddWarrantyToCartPlugin constructor.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     */
    public function __construct(RequestInterface $request, ProductRepository $productRepository)
    {
        $this->request = $request;
        $this->productRepository = $productRepository;
    }

    /**
     * Add warranty to the cart and set warranty-parent relation in the object instance level.
     *
     * @param \Magento\Quote\Model\Quote $quote
     * @param $parentQuoteItem
     * @return $this
     * @throws \Exception
     */
    public function afterAddProduct(Quote $quote, $parentQuoteItem)
    {
        $this->quote = $quote;
        $this->parentQuoteItem = $parentQuoteItem;

        $warrantyParam = $this->request->getParam('warranty');

        //if warranty parameter is present, then this is an add-to-cart action from PDP
        if ($parentQuoteItem->getProduct()->getTypeId() == 'virtual'
            || !$warrantyParam || !is_array($warrantyParam) || count($warrantyParam) === 0) {
            return $parentQuoteItem;
        }

        $this->addWarrantyIntoCart($warrantyParam);

        return $parentQuoteItem;
    }

    /**
     * Add warranty to the cart
     *
     * @param array $warranties
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addWarrantyIntoCart(array $warranties)
    {
        foreach ($warranties as $warrantyId) {
            /**
             * warranty wont be added if any other product is using it or the current product in the context has
             * a warranty associated with it.
             */
            if ($this->warrantyCanAddtoCart($warrantyId)) {
                throw new \Exception(
                    __('You cannot add the product to the cart with the selected warranty')
                );
                return $this;
            }

            $warrantyQty = $this->request->getParam('qty') ? (int)$this->request->getParam('qty') : 1;
            $parentProductId = $this->request->getParam('product', false);
            $warrantyProduct = $this->_getProduct((int)$warrantyId);
            $this->quote->addProduct($warrantyProduct, $warrantyQty);

            /**
             * set warranty-parent relation both in warranty and in parent.
             * Note this relation establishment only on the object instance level. We cannot save the relation
             * in the quote item because the quote items do not have item_id at this stage. So we will establish
             * the "real" relation only after cart->save() is performed. Look AddWarrantyToCart Observer.
             */
            if ($parentProductId) {
                $this->quote->getItemByProduct($warrantyProduct)
                    ->setWarrantyParentItem($this->quoteItemByProductId($parentProductId));

                $this->quoteItemByProductId($parentProductId)
                    ->setWarrantyChildItem($this->quote->getItemByProduct($warrantyProduct));
            }
        }

        return $this;
    }

    /**
     * Checks whether warranty product can be added to the cart.
     *
     * It wont be added, if the warranty in the context is already using by any other quote item.
     * It wont be added, if the parent product in the context already uses any other warranty item.
     *
     * @param int|string $warrantyId
     * @return bool
     */
    protected function warrantyCanAddtoCart($warrantyId)
    {
        //if warranty has any other parent, then do not want to proceed
        $warrantyParent = $this->warrantyParent($warrantyId);
        if ($warrantyParent) {
            return true;
        }

        //if the parent product in the add-to-cart context already has a warranty child, then dont want to proceed
        $parentProductId = $this->request->getParam('product', false);
        if ($parentProductId) {
            $parentQuoteItem = $this->quoteItemByProductId($parentProductId);

            if (!$parentQuoteItem->getItemId()) {
                return false;
            }

            foreach ($this->quote->getItemsCollection() as $item) {
                if ($item->getWarrantyParentItemId() == $parentQuoteItem->getItemId()) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Provide Warranty parent quote item based on the warranty product id passed.
     *
     * @param int|string $warrantyId
     * @return bool|false|\Magento\Quote\Model\Quote\Item
     */
    public function warrantyParent($warrantyId)
    {
        $warrantyParent = false;

        foreach ($this->quote->getItemsCollection() as $quoteItem) {
            if ($quoteItem->getProductId() == $warrantyId) {
                $warrantyParentItemId = (int)$quoteItem->getWarrantyParentItemId();
                $warrantyParent = $this->quote->getItemById($warrantyParentItemId);
                break;
            }
        }

        return $warrantyParent;
    }

    /**
     * Provide quote item by the product id passed.
     *
     * @param int|string $productId
     * @return bool|mixed
     */
    public function quoteItemByProductId($productId)
    {
        $warranty = false;

        foreach ($this->quote->getItemsCollection() as $quoteItem) {
            if ($quoteItem->getProductId() == $productId) {
                $warranty = $quoteItem;
                break;
            }
        }

        return $warranty;
    }

    /**
     * Load a product.
     *
     * @param int|string $productId
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface|mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _getProduct($productId)
    {
        try {
            return $this->productRepository->getById($productId, false, $this->parentQuoteItem->getStoreId());
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}