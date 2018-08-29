<?php
/**
 * @package   Dyode
 * @author    kavitha@dyode.com
 * Date       23/08/2018
 */
namespace Dyode\Checkout\Model;

use \Dyode\Checkout\Api\CreditManagementInterface;

class CreditManagement extends \Magento\Framework\Model\AbstractModel implements CreditManagementInterface
{
    /**
     * @var \Magento\Quote\Api\CartRepositoryInterface
     */
    protected $cartRepository;

    /**
     * @param \Magento\Quote\Api\CartRepositoryInterface $cartRepository
     */
    public function __construct(
        \Magento\Quote\Api\CartRepositoryInterface $cartRepository
    ) {
        $this->cartRepository = $cartRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function apply($cartId)
    {
        /** @var \Magento\Quote\Api\Data\CartInterface $quote */
        $quote = $this->cartRepository->get($cartId);
        $quote->setUseCredit(true);
        $quote->collectTotals();
        $quote->save();
        return true;
    }

    public function removecredit($cartId){
      /** @var \Magento\Quote\Api\Data\CartInterface $quote */
      $quote = $this->cartRepository->getActive($cartId);
      $quote->getShippingAddress()->setCollectShippingRates(true);
      try {
          $quote->setUseCredit(false);
          $this->cartRepository->save($quote->collectTotals());
      } catch (\Exception $e) {
          throw new CouldNotDeleteException(__('Could not delete curacao credit'));
      }
        return true;
    }
}
