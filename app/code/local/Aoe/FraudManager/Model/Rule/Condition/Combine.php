<?php

class Aoe_FraudManager_Model_Rule_Condition_Combine extends Aoe_FraudManager_Model_Rule_Condition_Abstract
{
    /**
     * Internal constructor not depended on params. Can be used for object initialization
     */
    protected function _construct()
    {
        $this->setType('Aoe_FraudManager/Rule_Condition_Combine');
        $this->setName($this->__('Conditions Combination'));
        $this->arrayKeys[] = 'aggregator';
        $this->arrayKeys[] = 'value';
    }

    /**
     * @return string
     */
    public function getSelfType()
    {
        return $this->getType();
    }

    /**
     * @return string|null
     */
    public function getAggregator()
    {
        if (is_null($this->getData('aggregator'))) {
            $options = array_keys($this->getAggregatorOptions());
            $this->setAggregator(reset($options));
        }

        return $this->getData('aggregator');
    }

    /**
     * @param string $aggregator
     *
     * @return $this;
     */
    public function setAggregator($aggregator)
    {
        return $this->setData('aggregator', $aggregator);
    }

    /**
     * @return string|null
     */
    public function getValue()
    {
        if (is_null($this->getData('value'))) {
            $options = array_keys($this->getValueOptions());
            $this->setValue(reset($options));
        }

        return $this->getData('value');
    }

    /**
     * @param string $value
     *
     * @return $this;
     */
    public function setValue($value)
    {
        return $this->setData('value', $value);
    }

    /**
     * @return Aoe_FraudManager_Model_Rule_Condition_Interface[]
     */
    public function getConditions()
    {
        if (!is_array($this->getData('conditions'))) {
            $this->setConditions(array());
        }

        return $this->getData('conditions');
    }

    /**
     * Set conditions, if current prefix is undefined use 'conditions' key
     *
     * @param Aoe_FraudManager_Model_Rule_Condition_Interface[] $conditions
     *
     * @return $this
     */
    public function setConditions(array $conditions)
    {
        return $this->setData('conditions', $conditions);
    }

    /**
     * @param Aoe_FraudManager_Model_Rule_Condition_Interface $condition
     *
     * @return $this
     */
    public function addCondition(Aoe_FraudManager_Model_Rule_Condition_Interface $condition)
    {
        $condition->setRule($this->getRule());

        $conditions = $this->getConditions();
        if (!$condition->getId()) {
            $id = uniqid($this->getId() . '--');
            while (isset($conditions[$id])) {
                $id = uniqid($this->getId() . '--');
            }
            $condition->setId($id);
        }

        $conditions[$condition->getId()] = $condition;

        $this->setConditions($conditions);

        return $this;
    }

    public function removeCondition(Aoe_FraudManager_Model_Rule_Condition_Interface $condition)
    {
        $conditions = $this->getConditions();
        unset($conditions[$condition->getId()]);
        $this->setConditions($conditions);
        return $this;
    }

    /**
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        $conditions = $this->getConditions();

        if (empty($conditions)) {
            return true;
        }

        $all = ($this->getAggregator() === 'all');
        $true = (bool)$this->getValue();

        foreach ($conditions as $condition) {
            $validated = $condition->validate($object);
            if ($all && $validated !== $true) {
                return false;
            } elseif (!$all && $validated === $true) {
                return true;
            }
        }

        return $all;
    }

    //==================================================
    //=[ HTML Generation ]==============================
    //==================================================

    public function getHtml()
    {
        $html = parent::getHtml();

        $htmlId = $this->getForm()->getHtmlIdPrefix() . $this->getId() . '__children' . $this->getForm()->getHtmlIdSuffix();
        $html .= '<ul id="' . $htmlId . '" class="rule-param-children">';

        foreach ($this->getConditions() as $condition) {
            $html .= '<li>' . $condition->getHtml() . '</li>';
        }

        $html .= '<li>' . $this->getNewChildElement()->getHtml() . '</li>';

        $html .= '</ul>';

        return $html;
    }

    public function getConditionConfigHtml()
    {
        return $this->__(
            'If %s of these conditions are %s:',
            $this->getAggregatorElement()->getHtml(),
            $this->getValueElement()->getHtml()
        );
    }

    public function getAggregatorOptions()
    {
        $options = array(
            'all' => $this->__('ALL'),
            'any' => $this->__('ANY'),
        );

        return $options;
    }

    public function getAggregatorName()
    {
        $options = $this->getAggregatorOptions();
        return (isset($options[$this->getAggregator()]) ? $options[$this->getAggregator()] : '');
    }

    public function getAggregatorElement()
    {
        $element = $this->getForm()->addField(
            $this->getId() . '__aggregator',
            'select',
            array(
                'name'    => $this->getId() . '[aggregator]',
                'value'   => $this->getAggregator(),
                'label'   => $this->getAggregatorName(),
                'options' => $this->getAggregatorOptions(),
            )
        );

        $element->setRenderer(Mage::getSingleton('Aoe_FraudManager/Form_Element_Renderer_Editable'));

        return $element;
    }

    public function getValueOptions()
    {
        $options = array(
            '1' => $this->__('TRUE'),
            '0' => $this->__('FALSE'),
        );

        return $options;
    }

    public function getValueName()
    {
        $options = $this->getValueOptions();
        return (isset($options[$this->getValue()]) ? $options[$this->getValue()] : '');
    }

    public function getValueElement()
    {
        $element = $this->getForm()->addField(
            $this->getId() . '__value',
            'select',
            array(
                'name'    => $this->getId() . '[value]',
                'value'   => $this->getValue(),
                'label'   => $this->getValueName(),
                'options' => $this->getValueOptions(),
            )
        );

        $element->setRenderer(Mage::getSingleton('Aoe_FraudManager/Form_Element_Renderer_Editable'));

        return $element;
    }

    /**
     * Get conditions selectors
     *
     * @return array
     */
    public function getNewChildOptions()
    {
        $conditions = array('' => $this->__('Please choose a condition to add...'));

        // Add self reference for recursive combinations
        $conditions[$this->getSelfType()] = $this->getName();

        // Fire an event to add additional conditions
        $container = new Varien_Object();
        Mage::dispatchEvent('aoe_fraudmanager_rule_condition_combine_additional', array('parent' => $this, 'container' => $container));
        if ($additional = $container->getConditions()) {
            $conditions = array_merge($conditions, $additional);
        }

        return $conditions;
    }

    public function getNewChildName()
    {
        return $this->__('Add');
    }

    public function getNewChildElement()
    {
        $element = $this->getForm()->addField(
            $this->getId() . '__new_child',
            'select',
            array(
                'name'    => $this->getId() . '[new_child]',
                'label'   => $this->getNewChildName(),
                'options' => $this->getNewChildOptions(),
            )
        );

        $element->setRenderer(Mage::getSingleton('Aoe_FraudManager/Form_Element_Renderer_Newchild'));

        return $element;
    }

    //==================================================
    //=[ Serialization / Deserialization ]==============
    //==================================================
    public function toArray()
    {
        $out = parent::toArray();

        foreach ($this->getConditions() as $condition) {
            $out['conditions'][] = $condition->toArray();
        }

        return $out;
    }

    public function loadArray(array $data)
    {
        parent::loadArray($data);

        if (array_key_exists('conditions', $data) && is_array($data['conditions'])) {
            foreach ($data['conditions'] as $conditionData) {
                try {
                    $modelClass = $conditionData['type'];
                    if (empty($modelClass)) {
                        continue;
                    }

                    $model = Mage::getSingleton($modelClass);
                    if (!$model || !$model instanceof Aoe_FraudManager_Model_Rule_Condition_Interface) {
                        continue;
                    }

                    $condition = clone $model;
                    if ($condition) {
                        $this->addCondition($condition);
                        $condition->loadArray($conditionData);
                        if ($condition instanceof Aoe_FraudManager_Model_Rule_Condition_Combine) {
                            $conditions = $condition->getConditions();
                            if (empty($conditions)) {
                                $this->removeCondition($condition);
                            }
                        }
                    }
                } catch (Exception $e) {
                    Mage::logException($e);
                }
            }
        }

        return $this;
    }
}
