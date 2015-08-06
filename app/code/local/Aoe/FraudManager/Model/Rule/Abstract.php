<?php

abstract class Aoe_FraudManager_Model_Rule_Abstract extends Mage_Core_Model_Abstract
{
    /**
     * Store rule combine conditions model
     *
     * @var Aoe_FraudManager_Model_Rule_Condition_Root
     */
    protected $root;

    /**
     * Form object used to create condition elements
     *
     * @var Varien_Data_Form
     */
    protected $conditionsForm;

    /**
     * Retrieve rule combine conditions model
     *
     * @return Aoe_FraudManager_Model_Rule_Condition_Root
     */
    public function getConditions()
    {
        $conditions = $this->getData('conditions');
        if (!$conditions instanceof Aoe_FraudManager_Model_Rule_Condition_Root) {
            if (is_array($conditions) && isset($conditions['type'])) {
                $root = Mage::getModel($conditions['type']);
                if (!$root instanceof Aoe_FraudManager_Model_Rule_Condition_Root) {
                    Mage::throwException('Invalid parameter');
                }
                $root->setRule($this);
                $root->loadArray($conditions);
            } else {
                $root = Mage::getModel('Aoe_FraudManager/Rule_Condition_Root');
                $root->setRule($this);
            }

            $this->setData('conditions', $root);
            $conditions = $root;
        }

        return $conditions;
    }

    /**
     * Set rule combine conditions model
     *
     * @param Aoe_FraudManager_Model_Rule_Condition_Root|array $conditions
     *
     * @return $this
     */
    public function setConditions($conditions)
    {
        if ($conditions instanceof Aoe_FraudManager_Model_Rule_Condition_Root) {
            $this->root = $conditions;
            $this->setData('conditions', $this->root);
        } elseif (is_array($conditions)) {
            $this->setData('conditions', $conditions);
            $this->getConditions();
        } else {
            Mage::throwException('Invalid parameter');
        }

        return $this;
    }

    /**
     * Rule form getter
     *
     * @return Varien_Data_Form
     */
    public function getConditionsForm()
    {
        if (!$this->conditionsForm) {
            $this->conditionsForm = new Varien_Data_Form();
            $this->conditionsForm->setHtmlIdPrefix('rule__conditions__');
            $this->conditionsForm->setFieldNameSuffix('rule[conditions]');
            $this->conditionsForm->addType('select', Mage::getConfig()->getModelClassName('Aoe_FraudManager/Form_Element_Select'));
        }

        return $this->conditionsForm;
    }

    /**
     * Validate rule conditions to determine if rule can run
     *
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        return $this->getConditions()->validate($object);
    }
}
