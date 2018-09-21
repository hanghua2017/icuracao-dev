<?php


namespace Dyode\Linkaccount\Block\Verify;

class Index extends \Magento\Framework\View\Element\Template
{
     /**
     * Construct
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        array $data = []
    )
    {
        parent::__construct($context, $data);
    }

    /**
     * Get form action URL for POST request
     *
     * @return string
     */
    public function getFormAction()
    {
        return '/linkaccount/verify/index';

    }
    public function getCodeFormAction()
    {
        return '/linkaccount/verify/codeverify';

    }

}