<?php

class Aoe_FraudManager_Model_Form_Element_Renderer_Newchild implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->addClass('element-value-changer');

        $html = '&nbsp;<span class="rule-param rule-param-new-child">';

        $html .= '<a href="javascript:void(0)" class="label">';
        $src = Mage::getDesign()->getSkinUrl('images/rule_component_add.gif');
        $html .= '<img src="' . $src . '" class="rule-param-add v-middle" alt="" title="' . $element->getLabel() . '"/>';
        $html .= '</a>';

        $html .= '<span class="element">';
        $html .= $element->getElementHtml();
        $html .= '</span>';

        $html .= '</span>&nbsp;';

        return $html;
    }
}
