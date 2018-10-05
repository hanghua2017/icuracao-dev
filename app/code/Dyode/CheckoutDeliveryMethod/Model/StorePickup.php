<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       06/09/2018
 */
namespace Dyode\CheckoutDeliveryMethod\Model;

use \Dyode\CheckoutDeliveryMethod\Api\StorePickupInterface;

class CreditManagement extends \Magento\Framework\Model\AbstractModel implements StorePickupInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;
    protected $_customerSession;
    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->_customerSession = $customerSession;
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function selection($zipcode)
    {

        return $zipcode;
    }
}
