<?php

namespace Dyode\Signifyd\Controller\Index;

/**
 * Class  Index
 * @category Dyode
 * @package  Dyode_Signifyd
 * @author   Nithin
 */
class Index extends \Magento\Framework\App\Action\Action
{
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Dyode\Signifyd\Model\Signifyd $signifydModel
    ) {
        $this->_pageFactory = $pageFactory;
        $this->signifydModel = $signifydModel;
        return parent::__construct($context);
    }

    /**
     * function name : execute
     * description : used for testing purpose
     */
    public function execute()
    {
        //uncomment the bellow code for testing this module using controller URL
        //$this->signifydModel->processSignifyd('000000008');
    }
}
