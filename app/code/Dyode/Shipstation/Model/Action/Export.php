<?php

namespace Dyode\Shipstation\Model\Action;

use Exception;

class Export
{

    /**
     * Write the order in xml file
     *
     * @param \Magento\Sales\Model\Order $order order details
     *
     * @return order
     */
    private function _writeOrder($order)
    {
        $this->_xmlData .= "\t<Order>\n";
        $this->_addFieldToXML("OrderNumber", $order->getIncrementId());
        $this->_addFieldToXML("OrderDate", $order->getCreatedAt());
        $this->_addFieldToXML("OrderStatus", $order->getStatus());
        $this->_addFieldToXML("LastModified", $order->getUpdatedAt());
        //Get the shipping method name and carrier name
        $this->_addFieldToXML(
            "ShippingMethod", 
            $order->getShippingDescription()
        );
        //Check for the price type
        if ($this->_priceType) {
            $orderTotal = $order->getBaseGrandTotal();
            $orderTax = $order->getBaseTaxAmount();
            $orderShipping = $order->getBaseShippingAmount();
        } else {
            $orderTotal = $order->getGrandTotal();
            $orderTax = $order->getTaxAmount();
            $orderShipping = $order->getShippingAmount();
        }

        $this->_addFieldToXML("OrderTotal", $orderTotal);
        $this->_addFieldToXML("TaxAmount", $orderTax);
        $this->_addFieldToXML("ShippingAmount", $orderShipping);
        $estimateNumber = (!empty($order->getData('estimatenumber'))) ? $order->getData('estimatenumber') : '';
        $this->_addFieldToXML("CustomField2", $estimateNumber);
        $this->_addFieldToXML(
            "InternalNotes",
            '<![CDATA[' . $order->getCustomerNote() .']]>'
        );
        //Get the gift message info
        $this->_getGiftMessageInfo($order);
        //Customer details
        $this->_xmlData .= "\t<Customer>\n";
        $this->_addFieldToXML("CustomerCode", $order->getCustomerEmail());
        $this->_getBillingInfo($order); //call to the billing info function
        $this->_getShippingInfo($order); //call to the shipping info function
        $this->_xmlData .= "\t</Customer>\n";
        $this->_xmlData .= "\t<Items>\n";
        $this->_orderItem($order); //call to the order items function
        //Get the order discounts
        if ($this->_importDiscount && $order->getDiscountAmount() != '0.0000') {
            $this->_getOrderDiscounts($order);
        }

        $this->_xmlData .= "\t</Items>\n";
        $this->_xmlData .= "\t</Order>\n";
    }

    /**
     * Write the order item in xml response data
     *
     * @param \Magento\Sales\Model\Order $order order object
     *
     * @return order
     */
    private function _orderItem($order)
    {
        if (!empty($order->getItems())) {
            $imageUrl = '';
            foreach ($order->getItems() as $orderItem) {
                $type = $orderItem->getProductType();
                $isVirtual = $orderItem->getIsVirtual();
                if ($isVirtual) {
                    continue;
                }

                //Get the parent item from the order item
                $parentItem = $orderItem->getParentItem();
                $weight = $orderItem->getWeight();
                if ($this->_priceType) {
                    $price = $orderItem->getBasePrice();
                } else {
                    $price = $orderItem->getPrice();
                }

                $name = $orderItem->getName();
                $product = $orderItem->getProduct();
                // check for the product object to return the image resource.
                if (!empty($product)) {
                    $attribute = $orderItem->getProduct()->getResource()
                        ->getAttribute('small_image');

                    $imageUrl = $attribute->getFrontend()
                        ->getUrl($orderItem->getProduct());
                }
                
                if (!empty($parentItem)) {
                    $type = $parentItem->getProductType();
                    if ($type == $this->_typeBundle) {
                        //Remove child items from the response data
                        if (!$this->_importChild) {
                            continue;
                        }

                        $weight = $price = 0;
                    }

                    //set the item price from parent item price
                    if ($type == self::TYPE_CONFIGURABLE) {
                        if ($price == '0.0000' || $price == null ) {
                            if ($this->_priceType) {
                                $price = $parentItem->getBasePrice();
                            } else {
                                $price = $parentItem->getPrice();
                            }

                        }

                        $name = $parentItem->getName();
                    }

                    // Set the parent image url if the item image is not set.
                    $product = $parentItem->getProduct();
                    if (!$imageUrl && !empty($product)) {
                        $attribute = $parentItem->getProduct()->getResource()
                            ->getAttribute('small_image');

                        $imageUrl = $attribute->getFrontend()
                            ->getUrl($parentItem->getProduct());
                    }

                } else {
                    if ($type == self::TYPE_CONFIGURABLE) {
                        continue;
                    }

                }
                
                if (!empty($orderItem)) {
                    $this->_xmlData .= "\t<Item>\n";
                    $this->_addFieldToXML("SKU", $orderItem->getSku());
                    $this->_addFieldToXML("Name", '<![CDATA[' . $name . ']]>');
                    $this->_addFieldToXML("ImageUrl", $imageUrl);
                    $this->_addFieldToXML("Weight", $weight);
                    $this->_addFieldToXML("UnitPrice", $price);
                    $location = (!empty($orderItem->getData('pickup_location'))) ? $orderItem->getData('pickup_location') : '';
                    $this->_addFieldToXML("CustomField3", $location);
                    $this->_addFieldToXML(
                        "Quantity",
                        intval($orderItem->getQtyOrdered())
                    );
                    //Get the item level gift message info
                    $this->_getGiftMessageInfo($orderItem);
                    /*
                     * Check for the attributes
                     */
                    $this->_xmlData .="\t<Options>\n";
                    $list = [];
                    $attributeCodes = explode(',', $this->_attributes);
                    foreach ($attributeCodes as $attributeCode) {
                        $product = $orderItem->getProduct();
                        $data = '';
                        if (!empty($product)) {
                            $data = $orderItem->getProduct()
                                ->hasData($attributeCode);   
                        }

                        if ($attributeCode && $data) {
                            $attribute = $this->_eavConfig->getAttribute(
                                $this->_getEntityType(), $attributeCode
                            );
                            $name = $attribute->getFrontendLabel();
                            $inputType = $attribute->getFrontendInput();
                            if (in_array($inputType, ['select', 'multiselect'])) {
                                $value = $orderItem->getProduct()
                                    ->getAttributeText($attributeCode);
                            } else {
                                $value = $orderItem->getProduct()
                                    ->getData($attributeCode);
                            }

                            //Add option to xml data
                            if ($value) {
                                $this->_writeOption($name, $value);
                                $list[] = $name;
                            }

                        }

                    }

                    //custom attribute selection.
                    $this->_getCustomAttributes($orderItem);
                    $this->_xmlData .= "\t</Options>\n";
                    $this->_xmlData .= "\t</Item>\n";
                }
            }
        }
    }

}