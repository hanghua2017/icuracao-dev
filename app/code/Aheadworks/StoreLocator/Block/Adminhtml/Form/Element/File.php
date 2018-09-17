<?php
/**
 * Copyright 2018 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\StoreLocator\Block\Adminhtml\Form\Element;

/**
 * Location Widget Form File Element Block
 */
class File extends \Magento\Framework\Data\Form\Element\AbstractElement
{
    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $assetRepo;

    /**
     * Adminhtml data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $adminhtmlData = null;

    /**
     * @var \Magento\Framework\Url\EncoderInterface
     */
    protected $urlEncoder;

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $factoryElement
     * @param \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\Helper\Data $adminhtmlData
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\Helper\Data $adminhtmlData,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        $data = []
    ) {
        $this->adminhtmlData = $adminhtmlData;
        $this->assetRepo = $assetRepo;
        $this->urlEncoder = $urlEncoder;
        parent::__construct($factoryElement, $factoryCollection, $escaper, $data);
        $this->setType('file');
    }

    /**
     * Return Form Element HTML
     *
     * @return string
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
     * Return Delete File CheckBox HTML
     *
     * @return string
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
     * Return Delete CheckBox SPAN Class name
     *
     * @return string
     */
    protected function _getDeleteCheckboxSpanClass()
    {
        return 'delete-file';
    }

    /**
     * Return Delete CheckBox Label
     *
     * @return \Magento\Framework\Phrase
     */
    protected function _getDeleteCheckboxLabel()
    {
        return __('Delete File');
    }

    /**
     * Return File preview link HTML
     *
     * @return string
     */
    protected function _getPreviewHtml()
    {
        $html = '';
        if ($this->getValue() && !is_array($this->getValue())) {
            $image = [
                'alt' => __('Download'),
                'title' => __('Download'),
                'src'   => $this->assetRepo->getUrl('images/fam_bullet_disk.gif'),
                'class' => 'v-middle'
            ];
            $url = $this->_getPreviewUrl();
            $html .= '<span>';
            $html .= '<a href="' . $url . '">' . $this->_drawElementHtml('img', $image) . '</a> ';
            $html .= '<a href="' . $url . '">' . __('Download') . '</a>';
            $html .= '</span>';
        }
        return $html;
    }

    /**
     * Return Hidden element with current value
     *
     * @return string
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
     * Return Preview/Download URL
     *
     * @return string
     */
    protected function _getPreviewUrl()
    {
        return $this->adminhtmlData->getUrl(
            'aw_store_locator/location/viewfile',
            ['file' => $this->urlEncoder->encode($this->getValue())]
        );
    }

    /**
     * Return Element HTML
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
     * Return escaped value
     *
     * @param int|null $index
     * @return string|false
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
