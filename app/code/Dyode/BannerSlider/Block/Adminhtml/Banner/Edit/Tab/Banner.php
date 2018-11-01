<?php
namespace Dyode\BannerSlider\Block\Adminhtml\Banner\Edit\Tab;

/**
 * Banner Edit tab.
 * @category Dyode
 * @package  Dyode_BannerSlider
 * @module   BannerSlider
 * @author   Nithin <nithin@dyode.com>
 */
class Banner extends \Magestore\Bannerslider\Block\Adminhtml\Banner\Edit\Tab\Banner
{
    protected $_systemStore;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\DataObjectFactory $objectFactory,
        \Magestore\Bannerslider\Model\Banner $banner,
        \Magestore\Bannerslider\Model\ResourceModel\Value\CollectionFactory $valueCollectionFactory,
        \Magestore\Bannerslider\Model\SliderFactory $sliderFactory,
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Store\Model\System\Store $systemStore,
        \Magento\Framework\Stdlib\DateTime\Timezone $dateTime,
        array $data = []
    ) {
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $objectFactory, $banner, $valueCollectionFactory, $sliderFactory, $wysiwygConfig, $dateTime, $data);
    }

    /**
     * Prepare form.
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $form = parent::_prepareForm();

        $form = $form->getForm();

        $fieldset = $form->getElement('base_fieldset');

        $store_field = $fieldset->addField(
           'bannerstore_id',
           'multiselect',
           [
             'name'     => 'bannerstore_id[]',
             'label'    => __('Store Views'),
             'title'    => __('Store Views'),
             'required' => true,
             'values'   => $this->_systemStore->getStoreValuesForForm(false, true),
           ]
        );

        return $this;
    }
}
