<?php
/**
 * Dyode_CheckoutAddressStep Magento2 Module.
 *
 * Adding new checkout step in the one page checkout.
 *
 * @package   Dyode
 * @module    Dyode_CheckoutAddressStep
 * @author    Rajeev K Tomy <rajeev.ktomy@dyode.com>
 * @copyright Copyright © Dyode
 */
namespace Dyode\CheckoutAddressStep\Block\Checkout;

use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;

/**
 * CheckoutAddressStepLayoutProcessor
 *
 * This fill the address-step with the necessary children.
 */
class CheckoutAddressStepLayoutProcessor implements LayoutProcessorInterface
{

    /**
     * @var array
     */
    protected $jsLayout;

    /**
     * Fills address-step with necessary children
     * It also removes address related children from shipping step.
     *
     * @param array $jsLayout
     * @return array $jsLayout
     */
    public function process($jsLayout)
    {
        $this->jsLayout = $jsLayout;

        $jsLayout = $this->addCheckoutAddressStepChildren($jsLayout);
        $jsLayout = $this->removeShippingAddressFromShippingMethodStep($jsLayout);

        return $jsLayout;
    }

    /**
     * Prepare and fill address-step with children.
     *
     * @param array $jsLayout
     * @return array $jsLayout
     */
    public function addCheckoutAddressStepChildren(array $jsLayout)
    {
        $shippingAddressChildren = $this->jsLayout["components"]["checkout"]["children"]["steps"]["children"]
        ["shipping-step"]["children"]["shippingAddress"]["children"];

        foreach (array_keys($shippingAddressChildren) as $childrenName) {
            if (!in_array($childrenName, $this->shippingStepChildrenNames())) {
                unset($shippingAddressChildren[$childrenName]);
            }
        }

        $jsLayout["components"]["checkout"]["children"]["steps"]["children"]["address-step"]["children"]
        ["shippingAddress"]["children"] = $shippingAddressChildren;

        return $jsLayout;
    }

    /**
     * Removes shipping address related children from the shipping-step.
     *
     * @param array $jsLayout
     * @return array $jsLayout
     */
    public function removeShippingAddressFromShippingMethodStep(array $jsLayout)
    {
        $shippingAddressChildren = $this->jsLayout["components"]["checkout"]["children"]["steps"]["children"]
        ["shipping-step"]["children"]["shippingAddress"]["children"];

        foreach (array_keys($shippingAddressChildren) as $childrenName) {
            if (in_array($childrenName, $this->shippingStepChildrenNames())) {
                unset($shippingAddressChildren[$childrenName]);
            }
        }

        $jsLayout["components"]["checkout"]["children"]["steps"]["children"]
        ["shipping-step"]["children"]["shippingAddress"]["children"] = $shippingAddressChildren;

        return $jsLayout;
    }

    /**
     * Holds address related children names which are populating by default magento.
     *
     * @return array
     */
    protected function shippingStepChildrenNames()
    {
        return [
            'customer-email',
            'before-form',
            'before-field',
            'address-list',
            'address-list-additional-addresses',
            'shipping-address-fieldset',
            'billing-address',
        ];
    }
}