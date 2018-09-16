<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package Dyode
 * @module  Dyode_Catalog
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */


namespace Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View;


use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\Filter;
use Magento\Framework\DataObject;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;
use Magento\Review\Model\Review;

/**
 * Frequently Brought Together View Model Class
 */
class Fbt implements  ArgumentInterface
{

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

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
     * Fbt constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder    $searchCriteriaBuilder
     * @param \Magento\Review\Model\Review                    $review
     * @param \Magento\Catalog\Helper\Product                 $catalogHelper
     * @param \Magento\Framework\Pricing\Helper\Data          $priceHelper
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        Review $review,
        ProductHelper $catalogHelper,
        PriceHelper $priceHelper

    ) {
        $this->productRepository = $productRepository;
        $this->criteriaBuilder = $searchCriteriaBuilder;
        $this->reviewModel = $review;
        $this->catalogHelper = $catalogHelper;
        $this->priceHelper = $priceHelper;
    }

    /**
     * Determine whether FBT sections needs to be shown
     *
     * We allow only of all products involving are simple product.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function canShowFbt(Product $product)
    {

        //make sure current product is simple
        if ($product->getTypeId() !== 'simple') {
            return false;
        }

        $fbtProducts = $this->fbtProducts($product);

        //make sure fbt products exists.
        if (count($fbtProducts) <= 0) {
            return false;
        }

        //make sure all fbt products are simple
        foreach ($fbtProducts as $fbtProduct) {
            if ($fbtProduct->getTypeId() !== 'simple') {
                return false;
            }
        }

        return true;
    }

    /**
     * Collect FBT products of current product
     *
     * @todo Right now, it provides dummy data. This needs to be modified after backend is developed.
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Api\Data\ProductInterface[]
     */
    public function fbtProducts(Product $product)
    {
        $searchCriteria = $this->criteriaBuilder->addFilters([new Filter([
            Filter::KEY_FIELD => 'entity_id',
            Filter::KEY_CONDITION_TYPE => 'in',
            Filter::KEY_VALUE => [1, 9]
        ])])->create();
        return $this->productRepository->getList($searchCriteria)->getItems();

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
        $finalPrice =$product->getFinalPrice();

        if (!$regularPrice || !$finalPrice || $regularPrice == $finalPrice ) {
            return false;
        }

        return round((($regularPrice - $finalPrice)*100)/$regularPrice);
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
     * Rating summary (normalized to five) of the product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function productRatingSummary(Product $product)
    {
        if (!$product->getRatingSummary()) {
            $this->_attachReviewToProduct($product);
        }

        $rating = $product->getRatingSummary()->getRatingSummary();
        return number_format(($rating/100)*5, 1);
    }

    /**
     * Checks whether the product has customer rating associated with it.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool
     */
    public function productHasRating(Product $product)
    {
        if (!$product->getRatingSummary()) {
            $this->_attachReviewToProduct($product);
        }
        return (bool)$product->getRatingSummary()->getReviewsCount();
    }

    /**
     * Provide associated FBT products final price total.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool                           $format
     * @return float|int|string
     */
    public function getAddonPriceTotal(Product $product, $format = false)
    {
        $total = 0;

        foreach ($this->fbtProducts($product) as $fbtProduct) {
            $total+= $fbtProduct->getFinalPrice();
        }

        if ($format) {
            return $this->priceHelper->currency($total, true, false);
        }

        return $total;
    }

    /**
     * Provide total of FBT section, including the current product.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param bool                           $format
     * @return float|int|string
     */
    public function getFbtProductsTotal(Product $product, $format = false)
    {
        $total = $product->getFinalPrice() + $this->getAddonPriceTotal($product);

        if ($format) {
            return $this->priceHelper->currency($total, true, false);
        }

        return $total;
    }

    /**
     * Provide total details as js data.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return bool|string
     */
    public function getJsData(Product $product)
    {
        $productData = [
            'product' => [
                'id'    => (int)$product->getId(),
                'price' => (float)$product->getFinalPrice(),
            ],
        ];

        $fbtData = [];
        foreach ($this->fbtProducts($product) as $fbtProduct) {
            $fbtData[(string)$fbtProduct->getId()] = [
                'id'    => (int)$fbtProduct->getId(),
                "price" => (float)$fbtProduct->getFinalPrice(),
            ];
        }
        $fbtProductsData = [
            'fbtProducts' => $fbtData,
        ];

        $jsObject = new DataObject(array_merge($productData, $fbtProductsData));

        return $jsObject->toJson();
    }

    /**
     * Add review summary data to the product.
     *
     * @param \Magento\Catalog\Model\Product $product
     */
    protected function _attachReviewToProduct(Product $product)
    {
       $this->reviewModel->getEntitySummary($product, $product->getStoreId());
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
