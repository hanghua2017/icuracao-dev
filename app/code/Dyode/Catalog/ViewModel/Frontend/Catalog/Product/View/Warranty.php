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

namespace Dyode\Catalog\ViewModel\Frontend\Catalog\Product\View;


use Dyode\Catalog\Model\Product\Link;
use Magento\Catalog\Api\ProductLinkRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Cms\Block\Block;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Pricing\Helper\Data as PriceHelper;

class Warranty implements ArgumentInterface
{
    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Api\ProductLinkRepositoryInterface
     */
    protected $productLinkRepository;

    /**
     * @var \Magento\Cms\Block\Block
     */
    protected $cmsBlock;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $priceHelper;

    /**
     * @var [\Magento\Catalog\Model\Product]
     */
    protected $warrantyProducts;

    /**
     * Warranty constructor.
     *
     * @param \Magento\Catalog\Api\ProductRepositoryInterface     $productRepository
     * @param \Magento\Catalog\Api\ProductLinkRepositoryInterface $productLinkRepository
     * @param \Magento\Cms\Block\Block                            $cmsBlock
     * @param \Magento\Framework\Pricing\Helper\Data              $priceHelper
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        ProductLinkRepositoryInterface $productLinkRepository,
        Block $cmsBlock,
        PriceHelper $priceHelper
    ) {
        $this->productRepository = $productRepository;
        $this->productLinkRepository = $productLinkRepository;
        $this->cmsBlock = $cmsBlock;
        $this->priceHelper = $priceHelper;
    }

    /**
     * Set product of which warranties are needed.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
        return $this;
    }

    /**
     * Collect warranty products
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function warrantyProducts()
    {
        if (!$this->warrantyProducts) {
            $warrantyProductSkus = [];
            foreach ($this->productLinkRepository->getList($this->product) as $linkItem) {
                if ($linkItem->getLinkType() === Link::LINK_WARRANTY_CODE) {
                    $warrantyProductSkus[] = $linkItem->getLinkedProductSku();
                }
            }

            $fbtProducts = [];
            foreach ($warrantyProductSkus as $sku) {
                $fbtProducts[] = $this->productRepository->get($sku);
            }
            $this->warrantyProducts = $fbtProducts;
        }

        return $this->warrantyProducts;
    }

    /**
     * Provide formatted price of the product passed.
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return mixed
     */
    public function formattedPrice(Product $product)
    {
        return $this->priceHelper->currency($product->getFinalPrice(), true, false);
    }

    /**
     * Prepare json string to populate warranty-details modal content.
     *
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWarrantyJsData()
    {
        $jsData = ['warrantyInfo' => []];

        foreach ($this->warrantyProducts() as $warrantyProduct) {

            $jsData['warrantyInfo'][(string)$warrantyProduct->getId()] = [
                'id'           => $warrantyProduct->getId(),
                'price'        => $this->formattedPrice($warrantyProduct),
                'description'  => $warrantyProduct->getDescription(),
                'cmsBlockHtml' => $this->getCmsBlockHtml((int)$this->product->getData('warranty_cms_block')),
            ];
        }

        return json_encode($jsData);
    }

    /**
     * Cms block html
     *
     * @param int|string $blockId
     * @return string
     */
    public function getCmsBlockHtml($blockId)
    {
        if ($blockId) {
            return $this->cmsBlock->setBlockId($blockId)->toHtml();
        }

        return '';
    }
}