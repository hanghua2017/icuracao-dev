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
namespace Dyode\Catalog\Model\Product;

class Link extends \Magento\Catalog\Model\Product\Link
{
    const LINK_TYPE_FBT = 19;
    const LINK_CODE = 'fbt';
    const LINK_WARRANTY_CODE = 'warranty';
    const LINK_TYPE_WARRANTY = 29;

    /**
     * @return $this
     */
    public function useFbtLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_FBT);
        return $this;
    }

    /**
     * @return $this
     */
    public function useWarrantyLinks()
    {
        $this->setLinkTypeId(self::LINK_TYPE_WARRANTY);
        return $this;
    }
}
