<?php
/**
 * Dyode_DeliveryMethod Magento2 Module.
 *
 * Add a new checkout step in checkout
 *
 * @module    Dyode_DeliveryMethod
 * @copyright Copyright © Dyode
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 */
namespace Dyode\DeliveryMethod\Model;

use Magento\Checkout\Model\ConfigProviderInterface;
use Magento\Checkout\Model\Session;

/**
 * Delivery Config Provider Class
 *
 * Provide delivery options data to the checkout section.
 */
class DeliveryConfigProvider implements ConfigProviderInterface
{

    const DELIVERY_OPTION_SHIP_TO_HOME = 'ship_to_home';
    const DELIVERY_OPTION_STORE_PICKUP = 'store_pickup';

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * DeliveryConfigProvider constructor.
     *
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(Session $checkoutSession)
    {
        $this->checkoutSession = $checkoutSession;
    }

    /**
     * @todo This should also check in quote for any store selection.
     * @return array $config
     */
    public function getConfig()
    {
        $config = ['deliveryOptions' => false];

        foreach ($this->getQuote()->getItems() as $quoteItem) {
            $config['deliveryOptions'][] = [
                'quoteItemId'  => (int)$quoteItem->getItemId(),
                'deliveryType' => self::DELIVERY_OPTION_SHIP_TO_HOME,
            ];
        }

        return $config;
    }

    /**
     * @return \Magento\Quote\Model\Quote
     */
    public function getQuote()
    {
        return $this->checkoutSession->getQuote();
    }
}