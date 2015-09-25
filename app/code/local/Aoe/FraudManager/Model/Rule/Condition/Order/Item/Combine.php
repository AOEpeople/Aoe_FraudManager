<?php
class Aoe_FraudManager_Model_Rule_Condition_Order_Item_Combine extends Aoe_FraudManager_Model_Rule_Condition_Combine
{
    /**
     * Internal constructor not depended on params. Can be used for object initialization
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setType('Aoe_FraudManager/Rule_Condition_Order_Item_Combine');
        $this->setName('Conditions Combination');
    }

    /**
     * Get conditions selectors
     *
     * @return array
     */
    public function getNewChildOptions()
    {
        $conditions = ['' => $this->translate('Please choose a condition to add...')];

        // Fire an event to add conditions
        $container = new Varien_Object();
        Mage::dispatchEvent('aoe_fraudmanager_rule_condition_order_item_combine_conditions', ['parent' => $this, 'container' => $container]);
        if ($additional = $container->getConditions()) {
            $conditions = array_merge($conditions, $additional);
        }

        return $conditions;
    }
}
