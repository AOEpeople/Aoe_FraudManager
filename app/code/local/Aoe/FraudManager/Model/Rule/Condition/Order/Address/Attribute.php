<?php

abstract class Aoe_FraudManager_Model_Rule_Condition_Order_Address_Attribute extends Aoe_FraudManager_Model_Rule_Condition_Attribute
{
    protected $attributes = array(
        'name'   => array('Name', array('==', '!=', '{}', '!{}', 'RE')),
        'street' => array('Street', array('==', '!=', '{}', '!{}', 'RE')),
        'city'   => array('City', array('==', '!=', '{}', '!{}', 'RE')),
        'region' => array('Region', array('==', '!=', '{}', '!{}', 'RE')),
        //'email'  => array('Email', array('==', '!=', '{}', '!{}', 'RE')),
        'all'    => array('All', array('==', '!=', '{}', '!{}', 'RE')),
    );

    protected function getAttributeValue(Varien_Object $object)
    {
        $attribute = $this->getAttribute();
        if (empty($attribute)) {
            return null;
        }

        /** @var Mage_Sales_Model_Order_Address $object */
        if ($object instanceof Mage_Sales_Model_Order_Address && $attribute === 'all') {
            return $object->format("text");
        } else {
            return $object->getDataUsingMethod($attribute);
        }
    }

    public function validate(Varien_Object $object)
    {
        /** @var Mage_Sales_Model_Order_Address $object */
        if (!$object instanceof Mage_Sales_Model_Order_Address) {
            return false;
        }

        return parent::validate($object);
    }
}
