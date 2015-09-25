<?php

class Aoe_FraudManager_Model_Rule_Condition_Order_Item_Attribute extends Aoe_FraudManager_Model_Rule_Condition_Attribute
{
    protected $attributes = [
        'sku' => ['SKU', ['==', '!=', '{}', '!{}', 'RE']],
    ];

    protected function _construct()
    {
        parent::_construct();
        $this->setType('Aoe_FraudManager/Rule_Condition_Order_Item_Attribute');
        $this->setName('Order Item');
    }

    public function validate(Varien_Object $object)
    {
        /** @var Mage_Sales_Model_Order_Item $object */
        if (!$object instanceof Mage_Sales_Model_Order_Item) {
            return false;
        }

        return parent::validate($object);
    }
}
