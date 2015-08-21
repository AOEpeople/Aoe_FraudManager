<?php

class Aoe_FraudManager_Model_Config_Source_Order_HoldStatusesNone extends Aoe_FraudManager_Model_Config_Source_Order_HoldStatuses
{
    public function toOptionArray()
    {
        $options = parent::toOptionArray();

        array_shift($options);
        array_unshift($options, ['value' => '', 'label' => Mage::helper('Aoe_FraudManager')->__('-- None --')]);

        return $options;
    }
}
