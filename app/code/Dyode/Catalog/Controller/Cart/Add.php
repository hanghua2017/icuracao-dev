<?php
/**
 * Dyode_Catalog Magento2 Module.
 *
 * Extending Magento_Catalog
 *
 * @package Dyode
 * @module  Dyode_Catalog
 * @author  Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */
namespace Dyode\Catalog\Controller\Cart;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Checkout\Model\Session;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Data\Form\FormKey\Validator;

/**
 * Add to Cart controller
 *
 * This allows to add multiple product at a time to the cart.
 */
class Add extends \Magento\Framework\App\Action\Action

{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Framework\Data\Form\FormKey\Validator
     */
    protected $_formKeyValidator;

    /**
     * @var \Magento\Framework\Api\SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $jsonFactory;

    /**
     * @var \Magento\Checkout\Model\Cart
     */
    protected $cart;

    /**
     * Add constructor.
     *
     * @param \Magento\Framework\App\Action\Context            $context
     * @param \Magento\Checkout\Model\Session                  $checkoutSession
     * @param \Magento\Framework\Data\Form\FormKey\Validator   $formKeyValidator
     * @param \Magento\Checkout\Model\Cart                     $cart
     * @param \Magento\Catalog\Api\ProductRepositoryInterface  $productRepository
     * @param \Magento\Framework\Api\SearchCriteriaBuilder     $searchCriteriaBuilder
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        Session $checkoutSession,
        Validator $formKeyValidator,
        CustomerCart $cart,
        ProductRepositoryInterface $productRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        JsonFactory $jsonFactory
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->productRepository = $productRepository;
        $this->_formKeyValidator = $formKeyValidator;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->cart = $cart;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);

    }

    /**
     * Add product to shopping cart action
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute()
    {
        if (!$this->_formKeyValidator->validate($this->getRequest())) {
            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        try {
            $productIds = (array)json_decode($this->getRequest()->getParam('fbt_products'));

            /**
             * Check product availability
             */
            if (count($productIds) === 0) {
                return $this->goBack();
            }

            $this->cart->addProductsByIds($productIds);
            $this->cart->save();

            if (!$this->checkoutSession->getNoCartRedirect(true)) {
                if (!$this->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 items in your shopping cart.',
                        count($productIds)
                    );
                    $this->messageManager->addSuccessMessage($message);
                }
            }

            return $this->goBack();

        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('We can\'t add these items to your shopping cart right now.'));
            return $this->goBack();
        }
    }

    /**
     * Provides a JSON response
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    protected function goBack()
    {
        $result = $this->jsonFactory->create();
        return $result->setData(['success' => true]);
    }
}
