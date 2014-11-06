<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-06
 */
abstract class Aoe_FraudManager_Resource_Abstract extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Serialize serializable fields of the object
     *
     * @param Mage_Core_Model_Abstract $object
     */
    protected function _serializeFields(Mage_Core_Model_Abstract $object)
    {
        $this->serializeFields($object);
    }

    /**
     * Serialize serializable fields of the object
     *
     * @param Mage_Core_Model_Abstract $object
     */
    public function serializeFields(Mage_Core_Model_Abstract $object)
    {
        foreach ($this->_serializableFields as $field => $parameters) {
            if (count($parameters) >= 2) {
                $serializeDefault = $parameters[0];
                if (count($parameters) > 3) {
                    $unsetEmpty = (bool)$parameters[2];
                    $callback = $parameters[3];
                } else {
                    $unsetEmpty = isset($parameters[2]);
                    $callback = null;
                }
                $this->_serializeField($object, $field, $serializeDefault, $unsetEmpty, $callback);
            }
        }
    }


    /**
     * Unserialize serializable object fields
     *
     * @param Mage_Core_Model_Abstract $object
     */
    public function unserializeFields(Mage_Core_Model_Abstract $object)
    {
        foreach ($this->_serializableFields as $field => $parameters) {
            if (count($parameters) >= 2) {
                $unserializeDefault = $parameters[1];
                if (count($parameters) > 4) {
                    $callback = $parameters[4];
                } else {
                    $callback = null;
                }
                $this->_unserializeField($object, $field, $unserializeDefault, $callback);
            }
        }
    }

    public static function implodeArray($value)
    {
        if(is_array($value)) {
            $value = implode(",", $value);
        } else {
            $value = (string)$value;
        }
        return $value;
    }

    public static function explodeArray($value)
    {
        if(is_string($value)) {
            $value = array_filter(array_map('trim', explode(",", $value)));
        } else {
            $value = array();
        }
        return $value;
    }

    /**
     * Serialize specified field in an object
     *
     * @param Varien_Object $object
     * @param string        $field
     * @param null          $defaultValue
     * @param bool          $unsetEmpty
     * @param callable      $callback
     *
     * @return $this
     */
    protected function _serializeField(Varien_Object $object, $field, $defaultValue = null, $unsetEmpty = false, $callback = null)
    {
        if (!is_callable($callback)) {
            $callback = 'serialize';
        }

        $value = $object->getData($field);
        if (empty($value)) {
            if ($unsetEmpty) {
                $object->unsetData($field);
            } else {
                if (is_object($defaultValue) || is_array($defaultValue)) {
                    $defaultValue = call_user_func($callback, $defaultValue);
                }
                $object->setData($field, $defaultValue);
            }
        } elseif (is_array($value) || is_object($value)) {
            $object->setData($field, call_user_func($callback, $value));
        }

        return $this;
    }

    /**
     * Unserialize Varien_Object field in an object
     *
     * @param Varien_Object $object
     * @param string        $field
     * @param mixed         $defaultValue
     * @param callable      $callback
     */
    protected function _unserializeField(Varien_Object $object, $field, $defaultValue = null, $callback = null)
    {
        if (!is_callable($callback)) {
            $callback = 'unserialize';
        }

        $value = $object->getData($field);
        if (empty($value)) {
            $object->setData($field, $defaultValue);
        } elseif (!is_array($value) && !is_object($value)) {
            $object->setData($field, call_user_func($callback, $value));
        }
    }

    /**
     * Prepare data for save
     *
     * @param Mage_Core_Model_Abstract $object
     *
     * @return array
     */
    protected function _prepareDataForSave(Mage_Core_Model_Abstract $object)
    {
        $currentTime = Varien_Date::now();
        if ((!$object->getId() || $object->isObjectNew()) && !$object->getCreatedAt()) {
            $object->setCreatedAt($currentTime);
        }
        $object->setUpdatedAt($currentTime);
        $data = parent::_prepareDataForSave($object);
        return $data;
    }
}
