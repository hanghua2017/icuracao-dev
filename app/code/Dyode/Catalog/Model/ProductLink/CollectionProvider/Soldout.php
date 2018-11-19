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

namespace Dyode\Catalog\Model\ProductLink\CollectionProvider;

use Dyode\Catalog\Model\Product\Link;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductLink\CollectionProviderInterface;

class Soldout implements CollectionProviderInterface
{

    /**
     * @var \Dyode\Catalog\Model\Product\Link
     */
    protected $linkInstance;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * Fbt constructor.
     *
     * @param \Dyode\Catalog\Model\Product\Link $productLink
     */
    public function __construct(Link $productLink)
    {
        $this->linkInstance = $productLink;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinkedProducts(Product $product)
    {
        $this->product = $product;
        return $this->getSoldoutProducts();
    }

    /**
     * @return mixed
     */
    public function getSoldoutProducts()
    {
        if (!$this->product->getSoldoutProducts()) {
            $products = [];
            foreach ($this->getSoldoutProductCollection() as $product) {
                $products[] = $product;
            }
            $this->product->setSoldoutProducts($products);
        }
        return $this->product->getData('soldout_products');
    }

    /**
     * @return \Magento\Catalog\Model\ResourceModel\Product\Link\Product\Collection
     */
    public function getSoldoutProductCollection()
    {
        $collection = $this->getLinkInstance()->useSoldoutLinks()
            ->getProductCollection()
            ->setIsStrongMode();

        $collection->setProduct($this->product);

        return $collection;
    }

    /**
     * @return \Dyode\Catalog\Model\Product\Link
     */
    public function getLinkInstance()
    {
        return $this->linkInstance;
    }
}
