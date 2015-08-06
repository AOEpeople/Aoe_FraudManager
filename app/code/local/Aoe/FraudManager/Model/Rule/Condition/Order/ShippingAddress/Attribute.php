<?php

class Aoe_FraudManager_Model_Rule_Condition_Order_ShippingAddress_Attribute extends Aoe_FraudManager_Model_Rule_Condition_Order_Address_Attribute
{
    protected function _construct()
    {
        parent::_construct();
        $this->setType('Aoe_FraudManager/Rule_Condition_Order_ShippingAddress_Attribute');
        $this->setName('Shipping Address');
    }

    public function validate(Varien_Object $object)
    {
        /** @var Mage_Sales_Model_Order_Address $object */
        if (!$object instanceof Mage_Sales_Model_Order_Address && is_callable([$object, 'getShippingAddress'])) {
            $object = $object->getShippingAddress();
        }

        return parent::validate($object);
    }
}
