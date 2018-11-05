<?php
namespace Dyode\BannerSlider\Model\ResourceModel\Banner;

/**
 * Banner Collection
 * @category Magestore
 * @package  Dyode_BannerSlider
 * @module   BannerSlider
 * @author   Nithin <nithin@dyode>
 */
class Collection extends \Magestore\Bannerslider\Model\ResourceModel\Banner\Collection
{

    public function getBannerCollection($sliderId)
    {
        $storeViewId = $this->_storeManager->getStore()->getId();
        $dateTimeNow = $this->_stdTimezone->date()->format('Y-m-d H:i:s');

        /** @var \Magestore\Bannerslider\Model\ResourceModel\Banner\Collection $bannerCollection */
        $bannerCollection = $this->setStoreViewId($storeViewId)
            ->addFieldToFilter('slider_id', $sliderId)
            ->addFieldToFilter('status', \Magestore\Bannerslider\Model\Status::STATUS_ENABLED)
            ->addFieldToFilter('start_time', ['lteq' => $dateTimeNow])
            ->addFieldToFilter('end_time', ['gteq' => $dateTimeNow])
            ->addFieldToFilter('bannerstore_id',['in' => array('0',$storeViewId)])
            ->setOrder('order_banner', 'ASC');

        if ($this->_slider->getSortType() == \Magestore\Bannerslider\Model\Slider::SORT_TYPE_RANDOM) {
            $bannerCollection->setOrderRandByBannerId();
        }

        return $bannerCollection;
    }
}
