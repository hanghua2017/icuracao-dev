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

use Dyode\Checkout\Model\ConfigProvider;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Magento\Customer\Model\AttributeMetadataDataProvider;
use Magento\Ui\Component\Form\AttributeMapper;
use Magento\Checkout\Block\Checkout\AttributeMerger;

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
     * @var \Magento\Customer\Model\AttributeMetadataDataProvider
     */
    public $attributeMetadataDataProvider;

    /**
     * @var \Magento\Ui\Component\Form\AttributeMapper
     */
    public $attributeMapper;

    /**
     * @var \Magento\Checkout\Block\Checkout\AttributeMerger
     */
    public $merger;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Dyode\Checkout\Model\ConfigProvider
     */
    protected $configProvider;

    /**
     * CheckoutAddressStepLayoutProcessor constructor.
     *
     * @param \Magento\Customer\Model\AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param \Magento\Ui\Component\Form\AttributeMapper $attributeMapper
     * @param \Magento\Checkout\Block\Checkout\AttributeMerger $merger
     * @param \Dyode\Checkout\Model\ConfigProvider $configProvider
     */
    public function __construct(
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AttributeMapper $attributeMapper,
        AttributeMerger $merger,
        ConfigProvider $configProvider
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper = $attributeMapper;
        $this->merger = $merger;
        $this->configProvider = $configProvider;
    }

    /**
     * Fills address-step with necessary children
     * It also removes address related children from shipping step.
     *
     * @param array $jsLayout
     * @return array $jsLayout
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function process($jsLayout)
    {
        $this->jsLayout = $jsLayout;

        $jsLayout = $this->addCheckoutAddressStepChildren($jsLayout);
        $jsLayout = $this->removeShippingAddressFromShippingMethodStep($jsLayout);
        $jsLayout = $this->addBillingAddressIntoAddressStep($jsLayout);
        $jsLayout = $this->changeSummaryTotalsSortOrder($jsLayout);
        $jsLayout = $this->provideCustomDataToComponents($jsLayout);

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
            if (!in_array($childrenName, $this->addressStepChildrenNames())) {
                unset($shippingAddressChildren[$childrenName]);
            }
        }

        $jsLayout["components"]["checkout"]["children"]["steps"]["children"]["address-step"]["children"]
        ["shippingAddress"]["children"] = $shippingAddressChildren;

        //remove "company" field from the shipping address
        unset($jsLayout["components"]["checkout"]["children"]["steps"]["children"]["address-step"]["children"]
            ["shippingAddress"]["children"]["shipping-address-fieldset"]["children"]["company"]
        );

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
            if (in_array($childrenName, $this->addressStepChildrenNames())) {
                unset($shippingAddressChildren[$childrenName]);
            }
        }

        $jsLayout["components"]["checkout"]["children"]["steps"]["children"]
        ["shipping-step"]["children"]["shippingAddress"]["children"] = $shippingAddressChildren;

        return $jsLayout;
    }

    /**
     * Changing the sort order of summary totals as per the design.
     *
     * @param array $jsLayout
     * @return array $jsLayout
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function addBillingAddressIntoAddressStep(array $jsLayout)
    {
        $jsLayout = $this->updateShippingAddressPlaceHolders($jsLayout);

        $elements = $this->getAddressAttributes();

        //remove "company" field from the billing address
        unset($elements["company"]);

        $jsLayout['components']['checkout']['children']['steps']['children']['address-step']['children']
        ['shippingAddress']['children']['billing-address'] = $this->getCustomBillingAddressComponent($elements);

        $jsLayout = $this->updateBillingAddressPlaceHolders($jsLayout);

        return $jsLayout;
    }

    /**
     * Prepare billing address field for shipping step for physical product
     *
     * @param $elements
     * @return array
     */
    public function getCustomBillingAddressComponent($elements)
    {
        return [
            'component'       => 'Magento_Checkout/js/view/billing-address',
            'displayArea'     => 'billing-address',
            'provider'        => 'checkoutProvider',
            'deps'            => ['checkoutProvider'],
            'dataScopePrefix' => 'billingAddress',
            'config'          => [
                'template' => 'Dyode_CheckoutAddressStep/billing-address',
            ],
            'children'        => [
                'form-fields' => [
                    'component'   => 'uiComponent',
                    'displayArea' => 'additional-fieldsets',
                    'children'    => $this->merger->merge(
                        $elements,
                        'checkoutProvider',
                        'billingAddress',
                        [
                            'country_id' => [
                                'sortOrder' => 115,
                            ],
                            'region'     => [
                                'visible' => false,
                            ],
                            'region_id'  => [
                                'component'  => 'Magento_Ui/js/form/element/region',
                                'config'     => [
                                    'template'    => 'ui/form/field',
                                    'elementTmpl' => 'ui/form/element/select',
                                    'customEntry' => 'billingAddress.region',
                                ],
                                'validation' => [
                                    'required-entry' => true,
                                ],
                                'filterBy'   => [
                                    'target' => '${ $.provider }:${ $.parentScope }.country_id',
                                    'field'  => 'country_id',
                                ],
                            ],
                            'postcode'   => [
                                'component'  => 'Magento_Ui/js/form/element/post-code',
                                'validation' => [
                                    'required-entry' => true,
                                ],
                            ],
                            'company'    => [
                                'validation' => [
                                    'min_text_length' => 0,
                                ],
                            ],
                            'fax'        => [
                                'validation' => [
                                    'min_text_length' => 0,
                                ],
                            ],
                            'telephone'  => [
                                'validation' => [
                                    'phoneUS' => true,
                                ],
                                'config'     => [
                                    'tooltip' => [
                                        'description' => __('For delivery questions.'),
                                    ],
                                ],
                            ],
                        ]
                    ),
                ],
            ],
        ];
    }

    /**
     * Changing sort orders of summary totals.
     *
     * @param array $jsLayout
     * @return array $jsLayout
     */
    public function changeSummaryTotalsSortOrder(array $jsLayout)
    {
        $jsLayout["components"]["checkout"]["children"]["sidebar"]["children"]["summary"]["children"]
        ["totals"]["children"]['shipping']['sortOrder'] = 10;
        $jsLayout["components"]["checkout"]["children"]["sidebar"]["children"]["summary"]["children"]
        ["totals"]["children"]['tax']['sortOrder'] = 20;
        $jsLayout["components"]["checkout"]["children"]["sidebar"]["children"]["summary"]["children"]
        ["totals"]["children"]['discount']['sortOrder'] = 30;
        $jsLayout["components"]["checkout"]["children"]["sidebar"]["children"]["summary"]["children"]
        ["totals"]["children"]['curacao_discount']['sortOrder'] = 110;
        $jsLayout["components"]["checkout"]["children"]["sidebar"]["children"]["summary"]["children"]
        ["totals"]["children"]['subtotal']['sortOrder'] = 50;
        $jsLayout["components"]["checkout"]["children"]["sidebar"]["children"]["summary"]["children"]
        ["totals"]["children"]['before_grandtotal']['sortOrder'] = 60;

        return $jsLayout;
    }

    /**
     * Holds address related children names which are populating by default magento.
     *
     * @return array
     */
    protected function addressStepChildrenNames()
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

    /**
     * Get all visible address attribute
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getAddressAttributes()
    {
        /** @var \Magento\Eav\Api\Data\AttributeInterface[] $attributes */
        $attributes = $this->attributeMetadataDataProvider->loadAttributesCollection(
            'customer_address',
            'customer_register_address'
        );

        $elements = [];
        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if ($attribute->getIsUserDefined()) {
                continue;
            }
            $elements[$code] = $this->attributeMapper->map($attribute);
            if (isset($elements[$code]['label'])) {
                $label = $elements[$code]['label'];
                $elements[$code]['label'] = __($label);
            }
        }
        return $elements;
    }

    /**
     * Change place holders of shipping address form fields
     *
     * @param array $jsLayout
     * @return array $jsLayout
     */
    protected function updateShippingAddressPlaceHolders(array $jsLayout)
     {
        //street 1
        $jsLayout['components']['checkout']['children']['steps']['children']['address-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
        ['street']['children'][0]['placeholder'] = __('Street Address, P.O Box, Company name');

        //street 2
        $jsLayout['components']['checkout']['children']['steps']['children']['address-step']
        ['children']['shippingAddress']['children']['shipping-address-fieldset']['children']
        ['street']['children'][1]['placeholder'] = __('Apartment, Suite, Building');

        //telephone
        $jsLayout["components"]["checkout"]["children"]["steps"]["children"]["address-step"]["children"]
        ["shippingAddress"]["children"]["shipping-address-fieldset"]["children"]
        ["telephone"]['placeholder'] = __('(123) 456 7890');

        //add US-Phone number validation for shipping address telephone field
        $jsLayout["components"]["checkout"]["children"]["steps"]["children"]["address-step"]["children"]
        ["shippingAddress"]["children"]["shipping-address-fieldset"]["children"]
        ["telephone"]['validation']['phoneUS'] = true;

        return $jsLayout;
    }

    /**
     * Change placeholders of billing address form fields
     *
     * @param array $jsLayout
     * @return array $jsLayout
     */
    protected function updateBillingAddressPlaceHolders(array $jsLayout)
    {
        //street 1
        $jsLayout['components']['checkout']['children']['steps']['children']['address-step']['children']
        ['shippingAddress']['children']['billing-address']['children']['form-fields']['children']
        ['street']['children'][0]['placeholder'] = __('Street Address, P.O Box, Company name');

        //street 2
        $jsLayout['components']['checkout']['children']['steps']['children']['address-step']['children']
        ['shippingAddress']['children']['billing-address']['children']['form-fields']['children']
        ['street']['children'][1]['placeholder'] = __('Apartment, Suite, Building');

        //telephone
        $jsLayout['components']['checkout']['children']['steps']['children']['address-step']['children']
        ['shippingAddress']['children']['billing-address']['children']['form-fields']['children']
        ['telephone']['placeholder'] = __('(123) 456 7890');

        return $jsLayout;
    }

    /**
     * Provide component specific data to the component.
     *
     * This is a much better method to feed a component with an external data (server data)
     * instead of fetching it through the "window.checkoutConfig" global variable, which considered as a bad practice.
     *
     * @param array $jsLayout
     * @return array $jsLayout
     */
    public function provideCustomDataToComponents(array $jsLayout)
    {

        $configData = $this->configProvider->getConfig();
        $isUserLinked = false;

        if (isset($configData['curacaoPayment']['linked'])) {
            $isUserLinked = $configData['curacaoPayment']['linked'];
        }

        //add delivery messages into "quoteShippingList" component;
        $jsLayout["components"]["checkout"]["children"]["steps"]["children"]["shipping-step"]["children"]
        ["shippingAddress"]["children"]["shippingAdditional"]["children"]["quoteShippingList"]
        ['config']['shippingMethodDeliveryMessages'] = $this->configProvider->collectShippingMethodDeliveryMsgs();

        $jsLayout["components"]["checkout"]["children"]["sidebar"]["children"]["additional_summary"]["children"]
        ["continue_button"]['config']['isUserCuracaoLinked'] = $isUserLinked;

        return $jsLayout;
    }

}
