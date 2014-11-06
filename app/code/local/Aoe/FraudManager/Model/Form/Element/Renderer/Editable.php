<?php

class Aoe_FraudManager_Model_Form_Element_Renderer_Editable implements Varien_Data_Form_Element_Renderer_Interface
{
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $element->addClass('element-value-changer');
        $valueName = $element->getLabel();

        if ($valueName === '' || $valueName === null) {
            $valueName = '...';
        }

        if ($element->getShowAsText()) {
            $html = ' <input type="hidden" class="hidden" id="' . $element->getHtmlId()
                . '" name="' . $element->getName() . '" value="' . $element->getValue() . '"/> '
                . htmlspecialchars($valueName) . '&nbsp;';
        } else {
            $html = '&nbsp;<span class="rule-param">';

            /** @var Mage_Core_Model_Translate_Inline $translate */
            $translate = Mage::getSingleton('core/translate_inline');
            if ($translate->isAllowed()) {
                $valueName = Mage::helper('core/string')->truncate($valueName, 33, '...');
            }

            $html .= '<a href="javascript:void(0)" class="label">';
            $html .= Mage::helper('core')->escapeHtml($valueName);
            $html .= '</a>';

            $html .= '<span class="element"> ';
            $html .= $element->getElementHtml();
            if ($element->getExplicitApply()) {
                /** @var Aoe_FraudManager_Helper_Data $helper */
                $helper = Mage::helper('Aoe_FraudManager/Data');
                $url = $helper->getConditionApplyImageUrl();
                $label = $helper->getConditionApplyLabel();

                $html .= ' <a href="javascript:void(0)" class="rule-param-apply">';
                $html .= '<img src="' . $url . '" class="v-middle" alt="' . $label . '" title="' . $label . '" />';
                $html .= '</a> ';
            }
            $html .= '</span>';

            $html .= '</span>&nbsp;';
        }

        return $html;
    }
}
