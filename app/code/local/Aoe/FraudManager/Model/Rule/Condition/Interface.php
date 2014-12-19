<?php

interface Aoe_FraudManager_Model_Rule_Condition_Interface
{
    /**
     * @return string
     */
    public function getType();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getId();

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id);

    /**
     * @return Aoe_FraudManager_Model_Rule_Abstract|null
     */
    public function getRule();

    /**
     * @param Aoe_FraudManager_Model_Rule_Abstract $rule
     *
     * @return $this
     */
    public function setRule(Aoe_FraudManager_Model_Rule_Abstract $rule);

    /**
     * @return string
     */
    public function getHtml();

    /**
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object);

    /**
     * @return array
     */
    public function toArray();

    /**
     * @param array $data
     *
     * @return mixed
     */
    public function loadArray(array $data);
}
