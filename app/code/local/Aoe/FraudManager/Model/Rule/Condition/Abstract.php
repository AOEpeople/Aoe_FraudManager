<?php

/**
 * @method $this setType(string $type)
 * @method $this setName(string $name)
 */
abstract class Aoe_FraudManager_Model_Rule_Condition_Abstract extends Varien_Object implements Aoe_FraudManager_Model_Rule_Condition_Interface
{
    /**
     * module name
     *
     * @var string
     */
    protected $moduleName;

    /**
     * @var array
     */
    protected $arrayKeys = ['type'];

    public function getType()
    {
        return $this->getData('type');
    }

    public function getName()
    {
        return $this->getData('name');
    }

    /**
     * @return Aoe_FraudManager_Model_Rule_Abstract
     */
    public function getRule()
    {
        return parent::getRule();
    }

    /**
     * @param Aoe_FraudManager_Model_Rule_Abstract $rule
     *
     * @return $this
     */
    public function setRule(Aoe_FraudManager_Model_Rule_Abstract $rule)
    {
        return parent::setRule($rule);
    }

    /**
     * @return Varien_Data_Form
     */
    public function getForm()
    {
        return $this->getRule()->getConditionsForm();
    }

    /**
     * @param Varien_Object $object
     *
     * @return bool
     */
    abstract public function validate(Varien_Object $object);

    //==================================================
    //=[ HTML Generation ]==============================
    //==================================================

    public function getHtml()
    {
        $html = $this->getTypeElement()->getHtml();
        $html .= $this->getConditionConfigHtml();
        $html .= $this->getRemoveLinkHtml();

        return $html;
    }

    public function getTypeElement()
    {
        $element = $this->getForm()->addField(
            $this->getId() . '__type',
            'hidden',
            [
                'name'    => $this->getId() . '[type]',
                'value'   => $this->getType(),
                'no_span' => true,
                'class'   => 'hidden',
            ]
        );

        return $element;
    }

    abstract public function getConditionConfigHtml();

    public function getRemoveLinkHtml()
    {
        $src = Mage::getDesign()->getSkinUrl('images/rule_component_remove.gif');
        $html = ' <span class="rule-param"><a href="javascript:void(0)" class="rule-param-remove" title="' . $this->translate('Remove') . '"><img src="' . $src . '"  alt="" class="v-middle" /></a></span>';

        return $html;
    }

    //==================================================
    //=[ Serialization / Deserialization ]==============
    //==================================================

    public function toArray(array $attributes = [])
    {
        $out = [];

        foreach ($this->arrayKeys as $key) {
            $out[$key] = $this->getDataUsingMethod($key);
        }

        return $out;
    }

    public function loadArray(array $data)
    {
        if (isset($data['type'])) {
            if ($data['type'] != $this->getType()) {
                Mage::throwException('Invalid parameter');
            }
            unset($data['type']);
        }

        foreach ($this->arrayKeys as $key) {
            if (array_key_exists($key, $data)) {
                $this->setDataUsingMethod($key, $data[$key]);
            }
        }

        return $this;
    }

    //==================================================
    //=[ Extras ]=======================================
    //==================================================

    /**
     * Translate
     *
     * @return string
     */
    protected function translate()
    {
        if (!$this->moduleName) {
            $class = get_class($this);
            $this->moduleName = substr($class, 0, strpos($class, '_Model'));
        }

        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->moduleName);
        array_unshift($args, $expr);

        return Mage::app()->getTranslator()->translate($args);
    }
}
