<?php

class Aoe_FraudManager_Model_Form_Element_Select extends Varien_Data_Form_Element_Select
{
    protected function _prepareOptions()
    {
        $values = $this->getValues();
        if (empty($values)) {
            $options = $this->getOptions();
            if (is_array($options)) {
                $values = $this->convertOptionsToValues($options);
            } elseif (is_string($options)) {
                $values = array(array('value' => $options, 'label' => $options));
            }
            $this->setValues($values);
        }
    }

    protected function convertOptionsToValues(array $options)
    {
        $values = array();

        foreach ($options as $value => $label) {
            if (is_array($label)) {
                $values[] = array(
                    'label' => $value,
                    'value' => $this->convertOptionsToValues($label),
                );
            } else {
                $values[] = array(
                    'label' => $label,
                    'value' => $value,
                );
            }
        }

        return $values;
    }
}
