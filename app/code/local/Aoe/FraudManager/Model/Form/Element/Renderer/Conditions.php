<?php

class Aoe_FraudManager_Model_Form_Element_Renderer_Conditions implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        /** @var Aoe_FraudManager_Model_Rule_Condition_Root $value */
        $conditions = $element->getValue();
        if ($conditions instanceof Aoe_FraudManager_Model_Rule_Condition_Root) {
            return $conditions->getHtml();
        }

        return '';
    }
}
