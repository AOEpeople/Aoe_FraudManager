<?php

abstract class Aoe_FraudManager_Model_Rule_Condition_Order_Address_Attribute extends Aoe_FraudManager_Model_Rule_Condition_Attribute
{
    protected $attributes = array(
        'name'       => array('Name', array('==', '!=', '{}', '!{}', 'RE')),
        'street'     => array('Street', array('==', '!=', '{}', '!{}', 'RE')),
        'city'       => array('City', array('==', '!=', '{}', '!{}', 'RE')),
        'region'     => array('Region', array('==', '!=', '{}', '!{}', 'RE')),
        'country_id' => array('Country', array('==', '!=', 'RE')),
        'all'        => array('All', array('==', '!=', '{}', '!{}', 'RE')),
    );

    protected function getAttributeValue(Varien_Object $object)
    {
        $attribute = $this->getAttribute();
        if (empty($attribute)) {
            return null;
        }

        /** @var Mage_Sales_Model_Order_Address $object */
        if ($object instanceof Mage_Sales_Model_Order_Address && $attribute === 'all') {
            $value = $object->format("text");
        } else {
            $value = $object->getDataUsingMethod($attribute);
        }

        if (is_scalar($value)) {
            // Convert 2+ spaces in a row into a single space
            $value = preg_replace('/ {2,}/u', ' ', $value);

            // Remove leading/trailing spaces
            $value = trim($value);
        }

        return $value;
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
