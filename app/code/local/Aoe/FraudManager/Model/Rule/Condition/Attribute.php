<?php

/**
 * @method string|null getValueElementChooserUrl()
 * @method $this setValueElementChooserUrl(string $url)
 */
abstract class Aoe_FraudManager_Model_Rule_Condition_Attribute extends Aoe_FraudManager_Model_Rule_Condition_Abstract
{
    protected $attributes = array();
    protected $attributeOptions = null;
    protected $arrayOperators = array('()', '!()');

    /**
     * Internal constructor not depended on params. Can be used for object initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->arrayKeys[] = 'attribute';
        $this->arrayKeys[] = 'operator';
        $this->arrayKeys[] = 'value';
    }

    /**
     * @return array|false
     */
    public function getAttributeOptions()
    {
        if ($this->attributeOptions === null) {
            $this->attributeOptions = array_map(array($this, 'translate'), array_map('reset', $this->attributes));
            if (empty($this->attributeOptions)) {
                $this->attributeOptions = false;
            }
        }

        return $this->attributeOptions;
    }

    /**
     * @return string|null
     */
    public function getAttribute()
    {
        return $this->getData('attribute');
    }

    /**
     * @param string $attribute
     *
     * @return $this
     */
    public function setAttribute($attribute)
    {
        return $this->setData('attribute', $attribute);
    }

    public function getAttributeName()
    {
        $attribute = $this->getAttribute();

        if (is_null($attribute) || '' === $attribute) {
            return '...';
        }

        $options = $this->getAttributeOptions();
        if ($options === false || empty($options)) {
            return $attribute;
        }

        if (array_key_exists($attribute, $options)) {
            return $options[$attribute];
        }

        return '';
    }

    /**
     * @return array
     */
    public function getOperatorOptions()
    {
        $allowedOperators = array();
        if (isset($this->attributes[$this->getAttribute()]) && isset($this->attributes[$this->getAttribute()][1])) {
            $allowedOperators = $this->attributes[$this->getAttribute()][1];
        }

        return Mage::helper('Aoe_FraudManager/Condition')->getOperators($allowedOperators);
    }

    /**
     * @return string|null
     */
    public function getOperator()
    {
        if (is_null($this->getData('operator'))) {
            $options = array_keys($this->getOperatorOptions());
            $this->setOperator(reset($options));
        }

        return $this->getData('operator');
    }

    /**
     * @param string $operator
     *
     * @return $this
     */
    public function setOperator($operator)
    {
        return $this->setData('operator', $operator);
    }

    /**
     * @return string
     */
    public function getOperatorName()
    {
        $options = $this->getOperatorOptions();
        return (isset($options[$this->getOperator()]) ? $options[$this->getOperator()] : '');
    }

    /**
     * Check if value should be array
     *
     * Depends on operator input type
     *
     * @return bool
     */
    public function getOperatorIsArrayType()
    {
        return in_array($this->getOperator(), $this->arrayOperators);
    }

    /**
     * @return array|false
     */
    public function getValueOptions()
    {
        return false;
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        return $this->getData('value');
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        return $this->setData('value', $value);
    }

    public function getValueParsed()
    {
        $value = $this->getValue();

        if ($this->getOperatorIsArrayType() && is_string($value)) {
            $value = preg_split('#\s*[,;]\s*#', $value, null, PREG_SPLIT_NO_EMPTY);
        }

        return $value;
    }

    public function getValueName()
    {
        $value = $this->getValue();

        if (is_null($value) || '' === $value) {
            return '...';
        }

        $options = $this->getValueOptions();
        if ($options === false) {
            return $value;
        }

        if (is_array($value)) {
            $valueLabels = array();
            foreach ($options as $k => $v) {
                if (in_array($k, $value)) {
                    $valueLabels[] = $v;
                }
            }
            return implode(', ', $valueLabels);
        } elseif (array_key_exists($value, $options)) {
            return $options[$value];
        }

        return '';
    }

    //==================================================
    //=[ HTML Generation ]==============================
    //==================================================

    public function getConditionConfigHtml()
    {
        $html = $this->translate($this->getName()) . ' ';
        $html .= $this->getAttributeElement()->getHtml();
        $html .= $this->getOperatorElement()->getHtml();
        $html .= $this->getValueElement()->getHtml();
        $html .= $this->getChooserContainerHtml();
        return $html;
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getAttributeElement()
    {
        $attribute = $this->getAttribute();
        $options = $this->getAttributeOptions();

        if (is_array($options) && empty($attribute)) {
            $element = $this->getForm()->addField(
                $this->getId() . '__attribute',
                'select',
                array(
                    'name'    => $this->getId() . '[attribute]',
                    'options' => $this->getAttributeOptions(),
                    'value'   => $this->getAttribute(),
                    'label'   => $this->getAttributeName(),
                )
            );
        } else {
            $element = $this->getForm()->addField(
                $this->getId() . '__attribute',
                'text',
                array(
                    'name'         => $this->getId() . '[attribute]',
                    'value'        => $this->getAttribute(),
                    'label'        => $this->getAttributeName(),
                    'show_as_text' => true,
                )
            );
        }

        $element->setRenderer(Mage::getSingleton('Aoe_FraudManager/Form_Element_Renderer_Editable'));

        return $element;
    }

    /**
     * @return Varien_Data_Form_Element_Abstract
     */
    public function getOperatorElement()
    {
        $element = $this->getForm()->addField(
            $this->getId() . '__operator',
            'select',
            array(
                'name'    => $this->getId() . '[operator]',
                'options' => $this->getOperatorOptions(),
                'value'   => $this->getOperator(),
                'label'   => $this->getOperatorName(),
            )
        );

        $element->setRenderer(Mage::getSingleton('Aoe_FraudManager/Form_Element_Renderer_Editable'));

        return $element;
    }


    /**
     * Value element type will define renderer for condition value element
     *
     * @see Varien_Data_Form_Element
     * @return string
     */
    public function getValueElementType()
    {
        return 'text';
    }

    /**
     * Retrieve input type
     *
     * @return string
     */
    public function getValueElementInputType()
    {
        return 'string';
    }

    public function getValueElement()
    {
        $elementParams = array(
            'name'    => $this->getId() . '[value]',
            'value'   => $this->getValue(),
            'options' => $this->getValueOptions(),
            'label'   => $this->getValueName(),
        );

        $options = $this->getValueOptions();
        if (is_array($options)) {
            $elementParams['options'] = $options;
        }

        if ($this->getValueElementType() == 'date') {
            // date format intentionally hard-coded
            $elementParams['input_format'] = Varien_Date::DATE_INTERNAL_FORMAT;
            $elementParams['format'] = Varien_Date::DATE_INTERNAL_FORMAT;
        }

        $element = $this->getForm()->addField($this->getId() . '__value', $this->getValueElementType(), $elementParams);

        $element->setRenderer(Mage::getSingleton('Aoe_FraudManager/Form_Element_Renderer_Editable'));

        return $element;
    }

    public function getChooserContainerHtml()
    {
        $html = '';

        $url = $this->getValueElementChooserUrl();
        if ($url) {
            $html = '<div class="rule-chooser" url="' . htmlspecialchars($url) . '"></div>';
        }

        return $html;
    }

    public function validate(Varien_Object $object)
    {
        $attribute = $this->getAttribute();
        if (empty($attribute)) {
            return false;
        }

        return Mage::helper('Aoe_FraudManager/Condition')->validateValue($this->getOperator(), $this->getValueParsed(), $this->getAttributeValue($object));
    }

    protected function getAttributeValue(Varien_Object $object)
    {
        $attribute = $this->getAttribute();
        if (!empty($attribute)) {
            return $object->getDataUsingMethod($attribute);
        } else {
            return null;
        }
    }
}
