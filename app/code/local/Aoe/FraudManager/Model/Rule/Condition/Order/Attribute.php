<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-18
 */
class Aoe_FraudManager_Model_Rule_Condition_Order_Attribute extends Aoe_FraudManager_Model_Rule_Condition_Attribute
{
    protected $attributes = array(
        'grand_total'    => array('Grand Total', array('==', '!=', '<=', '<', '>=', '>')),
        'customer_email' => array('Email', array('==', '!=', '{}', '!{}', 'RE')),
    );

    protected $attributeOptions = null;

    /**
     * Internal constructor not depended on params. Can be used for object initialization
     */
    protected function _construct()
    {
        $this->setType('Aoe_FraudManager/Rule_Condition_Order_Attribute');
        $this->setName('Order');
    }

    public function validate(Varien_Object $object)
    {
        if (!$object instanceof Mage_Sales_Model_Order) {
            if (is_callable(array($object, 'getOrder'))) {
                $object = $object->getOrder();
                if (!$object instanceof Mage_Sales_Model_Order) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return parent::validate($object);
    }
}
