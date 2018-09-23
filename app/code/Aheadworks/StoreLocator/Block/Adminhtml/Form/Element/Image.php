<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block\Adminhtml\Form\Element;

use Magento\Backend\Helper\Data;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\CollectionFactory;
use Magento\Framework\Data\Form\Element\Factory;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Phrase;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\Asset\Repository;

/**
 * Class Image.
 */
class Image extends AbstractElement
{
    /**
     * @var Repository
     */
    protected $assetRepo;

    /**
     * @var Data
     */
    protected $adminhtmlData = null;

    /**
     * @var EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @param Factory $factoryElement
     * @param CollectionFactory $factoryCollection
     * @param Escaper $escaper
     * @param Data $adminhtmlData
     * @param Repository $assetRepo
     * @param EncoderInterface $urlEncoder
     * @param array $data
     */
    public function __construct(
        Factory $factoryElement,
        CollectionFactory $factoryCollection,
        Escaper $escaper,
        Data $adminhtmlData,
        Repository $assetRepo,
        EncoderInterface $urlEncoder,
        $data = []
    ) {
        $this->adminhtmlData = $adminhtmlData;
        $this->assetRepo = $assetRepo;
        $this->urlEncoder = $urlEncoder;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);

        $this->setType('file');
    }

    /**
     * Return Form Element HTML.
     *
     * @return string
     * @throws LocalizedException
     */
    public function getElementHtml()
    {
        $this->addClass('input-file');
        if ($this->getRequired()) {
            $this->removeClass('required-entry');
            $this->addClass('required-file');
        }

        $element = sprintf(
            '<div class="upload"><input id="%s" name="%s" %s />%s%s',
            $this->getHtmlId(),
            $this->getName(),
            $this->serialize($this->getHtmlAttributes()),
            $this->getAfterElementHtml(),
            $this->_getHiddenInput()
        );

        return $this->_getPreviewHtml() . $element . $this->_getDeleteCheckboxHtml() . '</div>';
    }

    /**
     * Return Delete File CheckBox HTML.
     *
     * @return string
     * @throws LocalizedException
     */
    protected function _getDeleteCheckboxHtml()
    {
        $html = '';
        if ($this->getValue() && !$this->getRequired() && !is_array($this->getValue())) {
            $checkboxId = sprintf('%s_delete', $this->getHtmlId());
            $checkbox = [
                'type' => 'checkbox',
                'name' => sprintf('%s[delete]', $this->getName()),
                'value' => '1',
                'class' => 'checkbox',
                'id' => $checkboxId
            ];
            $label = ['for' => $checkboxId];
            if ($this->getDisabled()) {
                $checkbox['disabled'] = 'disabled';
                $label['class'] = 'disabled';
            }

            $html .= '<span class="' . $this->_getDeleteCheckboxSpanClass() . '">';
            $html .= $this->_drawElementHtml('input', $checkbox) . ' ';
            $html .= $this->_drawElementHtml('label', $label, false) . $this->_getDeleteCheckboxLabel() . '</label>';
            $html .= '</span>';
        }
        return $html;
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName()
    {
        $name = $this->getData('name');
        return $name;
    }

    /**
     * Get the location id.
     *
     * @return int
     */
    public function getLocationId()
    {
        $locationId = $this->getData('location_id');
        return $locationId;
    }

    /**
     * Return Delete CheckBox Label.
     *
     * @return Phrase
     */
    protected function _getDeleteCheckboxLabel()
    {
        return __('Delete Image');
    }

    /**
     * Return Delete CheckBox SPAN Class name.
     *
     * @return string
     */
    protected function _getDeleteCheckboxSpanClass()
    {
        return 'delete-image';
    }

    /**
     * Return File preview link HTML.
     *
     * @return string
     * @throws LocalizedException
     */
    protected function _getPreviewHtml()
    {
        $html = '';
        if ($this->getValue() && !is_array($this->getValue())) {
            $url = $this->_getPreviewUrl();
            $imageId = sprintf('%s_image', $this->getHtmlId());
            $image = [
                'alt' => __('View Full Size'),
                'title' => __('View Full Size'),
                'src' => $url,
                'class' => 'small-image-preview v-middle',
                'height' => $this->getHeight(),
                'width' => $this->getWidth(),
                'id' => $imageId
            ];
            $link = ['href' => $url, 'onclick' => "imagePreview('{$imageId}'); return false;"];

            $html = sprintf(
                '<div class="preview">%s%s</a></div> ',
                $this->_drawElementHtml('a', $link, false),
                $this->_drawElementHtml('img', $image)
            );
        }
        return $html;
    }

    /**
     * Return Hidden element with current value.
     *
     * @return string
     * @throws LocalizedException
     */
    protected function _getHiddenInput()
    {
        return $this->_drawElementHtml(
            'input',
            [
                'type' => 'hidden',
                'name' => sprintf('%s[value]', $this->getName()),
                'id' => sprintf('%s_value', $this->getHtmlId()),
                'value' => $this->getEscapedValue()
            ]
        );
    }

    /**
     * Return Image URL.
     *
     * @return string
     * @throws LocalizedException
     */
    protected function _getPreviewUrl()
    {
        return $this->adminhtmlData->getUrl(
            'aw_store_locator/location/viewfile',
            [
                'location_id' => $this->getLocationId(),
                'type' => $this->getName(),
                'file' => $this->urlEncoder->encode($this->getValue())
            ]
        );
    }

    /**
     * Return Element HTML.
     *
     * @param string $element
     * @param array $attributes
     * @param bool $closed
     * @return string
     */
    protected function _drawElementHtml($element, array $attributes, $closed = true)
    {
        $parts = [];
        foreach ($attributes as $k => $v) {
            $parts[] = sprintf('%s="%s"', $k, $v);
        }

        return sprintf('<%s %s%s>', $element, implode(' ', $parts), $closed ? ' /' : '');
    }

    /**
     * Return escaped value.
     *
     * @param int|null $index
     * @return string|false
     * @throws LocalizedException
     */
    public function getEscapedValue($index = null)
    {
        if (is_array($this->getValue())) {
            return false;
        }
        $value = $this->getValue();
        if (is_array($value) && $index === null) {
            $index = 'value';
        }

        return parent::getEscapedValue($index);
    }
}
