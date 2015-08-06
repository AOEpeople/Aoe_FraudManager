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
                $values = [['value' => $options, 'label' => $options]];
            }
            $this->setValues($values);
        }
    }

    protected function convertOptionsToValues(array $options)
    {
        $values = [];

        foreach ($options as $value => $label) {
            if (is_array($label)) {
                $values[] = [
                    'label' => $value,
                    'value' => $this->convertOptionsToValues($label),
                ];
            } else {
                $values[] = [
                    'label' => $label,
                    'value' => $value,
                ];
            }
        }

        return $values;
    }
}
