<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package   Dyode
 * @module    Dyode_Catalog
 * @author    Kavitha <kavitha@dyode.com>
 * @copyright Copyright © Dyode
 */


namespace Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View;


use Dyode\Catalog\Model\Product\Link;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Review\Model\Review;
use Magento\CatalogInventory\Api\StockRegistryInterface;

/**
 * Sold Out Related Products View Model Class
 */
class Soldout implements ArgumentInterface
{

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\ProductLinkRepositoryInterface
     */
    protected $productLinkRepository;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * @var \Magento\Review\Model\Review
     */
    protected $reviewModel;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $catalogHelper;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var [\Magento\Catalog\Model\Product]
     */
    protected $soldoutProducts;
    
    /**
    * @var \Magento\CatalogInventory\Api\StockRegistryInterface
    */
    private $stockRegistry;

    /**
     * Soldout constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface     $productRepository
     * @param \Magento\Catalog\Api\ProductLinkRepositoryInterface $productLinkRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder        $searchCriteriaBuilder
     * @param \Magento\Review\Model\Review                        $review
     * @param \Magento\Catalog\Helper\Product                     $catalogHelper
     * @param \Magento\Framework\Pricing\Helper\Data              $priceHelper
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductLinkRepositoryInterface $productLinkRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Review $review,
        ProductHelper $catalogHelper,
        PriceHelper $priceHelper,
        StockRegistryInterface $stockRegistry

    ) {
        $this->productRepository = $productRepository;
        $this->productLinkRepository = $productLinkRepository;
        $this->criteriaBuilder = $searchCriteriaBuilder;
        $this->reviewModel = $review;
        $this->catalogHelper = $catalogHelper;
        $this->priceHelper = $priceHelper;
        $this->stockRegistry = $stockRegistry;
    }

    /**
    * Get the product stock data and methods.
    *
    * @return \Magento\CatalogInventory\Api\StockRegistryInterface
    */
    public function getStockRegistry()
    {
        return $this->stockRegistry;
    }

    /**
     * Determine whether Sold out sections needs to be shown
     *
     * We allow only of all products involving are simple product.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function canShowSoldout(Product $product)
    {

        //make sure current product is simple
        if ($product->getTypeId() !== 'simple') {
            return false;
        }

        //Check the product is in stock

        /** @var \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry */
        $stockRegistry = $this->getStockRegistry();
        
        // Get stock data for given product.
        $productStock = $stockRegistry->getStockItem($product->getId());

        // Get quantity of product.
        $productQty = $productStock->getQty();

        if($productQty > 0) {
            return false;
        }

        $soldoutProducts = $this->soldoutProducts($product);

        //make sure fbt products exists.
        if (count($soldoutProducts) <= 0) {
            return false;
        }

        return true;
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function soldoutProducts(Product $product)
    {
        if (!$this->soldoutProducts) {
            $soldoutProductSkus = [];
            foreach ($this->productLinkRepository->getList($product) as $linkItem) {
                if ($linkItem->getLinkType() === Link::LINK_SOLDOUT_CODE) {
                    $soldoutProductSkus[] = $linkItem->getLinkedProductSku();
                }
            }

            $soldoutProducts = [];
            foreach ($soldoutProductSkus as $sku) {
                $soldoutProducts[] = $this->productRepository->get($sku);
            }

            $this->soldoutProducts = $soldoutProducts;
        }

        return $this->soldoutProducts;
    }

    /**
     * Provides small image url of the product.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string $imageUrl
     */
    public function getProductImageUrl(Product $product)
    {
        return $this->catalogHelper->getSmallImageUrl($product);
    }

    /**
     * Calculate discount of the product, if it has special price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool|float|int
     */
    public function productDiscount(Product $product)
    {
        $regularPrice = $product->getPrice();
        $finalPrice = $product->getFinalPrice();

        if (!$regularPrice || !$finalPrice || $regularPrice == $finalPrice) {
            return false;
        }

        return round((($regularPrice - $finalPrice) * 100) / $regularPrice);
    }

    /**
     * Add currency symbol along with the product price.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return float|string
     */
    public function formattedPrice(Product $product)
    {
        return $this->priceHelper->currency($product->getFinalPrice(), true, false);
    }

    /**
     * Provides product Collection instance.
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    protected function _getCollection()
    {
        return $this->productCollectionFactory->create();
    }
}
