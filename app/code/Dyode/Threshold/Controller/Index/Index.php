<?php

namespace Dyode\Threshold\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Dyode\Threshold\Model\Threshold;

class Index extends Action
{

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $_pageFactory;

    /**
     * @var \Dyode\Threshold\Model\Threshold
     */
    protected $thresholdModel;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $pageFactory
     * @param \Dyode\Threshold\Model\Threshold $thresholdModel
     */
    public function __construct(Context $context, PageFactory $pageFactory, Threshold $thresholdModel)
    {
        $this->thresholdModel = $thresholdModel;
        $this->_pageFactory = $pageFactory;
        return parent::__construct($context);
    }

    /**
     * Main entry point.
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     * @throws \Exception
     */
    public function execute()
    {

        $this->thresholdModel->getThreshold();
    }

}

