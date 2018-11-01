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
