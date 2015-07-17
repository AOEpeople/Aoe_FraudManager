<?php

class Aoe_FraudManager_Model_Rule_Condition_Order_Address_Compare extends Aoe_FraudManager_Model_Rule_Condition_Order_Address_Attribute
{
    protected $attributes = array(
        'name'        => array('Full Name', array('==', '!=')),
        'firstname'   => array('First Name', array('==', '!=')),
        'lastname'    => array('Last Name', array('==', '!=')),
        'street_full' => array('Street', array('==', '!=')),
        'city'        => array('City', array('==', '!=')),
        'region'      => array('Region', array('==', '!=')),
        'country_id'  => array('Country', array('==', '!=')),
        'all'         => array('All', array('==', '!=')),
    );

    protected function _construct()
    {
        parent::_construct();
        $this->setType('Aoe_FraudManager/Rule_Condition_Order_Address_Compare');
        $this->setName('Compare Billing and Shipping Addresses');
    }

    public function validate(Varien_Object $object)
    {
        if ($object instanceof Mage_Sales_Model_Order) {
            $order = $object;
        } elseif ($object instanceof Mage_Sales_Model_Order_Address) {
            $order = $object->getOrder();
        } else {
            // Cannot test
            return false;
        }

        /** @var Mage_Sales_Model_Order_Address $object */
        $billingAddress = $order->getBillingAddress();
        $shippingAddress = $order->getShippingAddress();

        if (!$billingAddress || !$shippingAddress) {
            // Cannot test
            return false;
        }

        $attribute = $this->getAttribute();
        if (empty($attribute)) {
            // Cannot test
            return false;
        }

        return Mage::helper('Aoe_FraudManager/Condition')->validateValue($this->getOperator(), $this->getAttributeValue($billingAddress), $this->getAttributeValue($shippingAddress));
    }

    /**
     * @return array
     */
    public function getOperatorOptions()
    {
        $allowedOperators = parent::getOperatorOptions();

        if (isset($allowedOperators['=='])) {
            $allowedOperators['=='] = Mage::helper('Aoe_FraudManager')->__('is the same');
        }

        if (isset($allowedOperators['!='])) {
            $allowedOperators['!='] = Mage::helper('Aoe_FraudManager')->__('is not the same');
        }

        return $allowedOperators;
    }

    public function getConditionConfigHtml()
    {
        $html = $this->translate('Billing and Shipping') . ' ';
        $html .= $this->getAttributeElement()->getHtml();
        $html .= $this->getOperatorElement()->getHtml();
        $html .= $this->getChooserContainerHtml();
        return $html;
    }
}
