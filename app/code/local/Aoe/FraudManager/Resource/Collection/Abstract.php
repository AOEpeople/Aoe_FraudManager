<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-06
 */
abstract class Aoe_FraudManager_Resource_Collection_Abstract extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Post-process collection items to run afterLoad on each
     *
     * @return $this
     */
    protected function _afterLoad()
    {
        Varien_Data_Collection_Db::_afterLoad();

        foreach ($this->_items as $item) {
            /** @var Mage_Core_Model_Abstract $item */
            $this->getResource()->unserializeFields($item);
            $item->afterLoad();
            $item->setOrigData();
            if ($this->_resetItemsDataChanged) {
                $item->setDataChanges(false);
            }
        }

        Mage::dispatchEvent('core_collection_abstract_load_after', array('collection' => $this));

        if ($this->_eventPrefix && $this->_eventObject) {
            Mage::dispatchEvent($this->_eventPrefix.'_load_after', array($this->_eventObject => $this));
        }

        return $this;
    }

    /**
     * Join table to collection select
     *
     * @param string $table
     * @param string $cond
     * @param string $cols
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function join($table, $cond, $cols = '*')
    {
        if (is_array($table)) {
            foreach ($table as $k => $v) {
                $alias = $k;
                $table = $v;
                break;
            }
        } else {
            $alias = $table;
        }

        if (!isset($this->_joinedTables[$alias])) {
            $this->getSelect()->join(
                array($alias => $this->getTable($table)),
                $cond,
                $cols
            );
            $this->_joinedTables[$alias] = true;
        }
        return $this;
    }

    /**
     * Join table to collection select
     *
     * @param string $table
     * @param string $cond
     * @param string $cols
     *
     * @return Mage_Core_Model_Resource_Db_Collection_Abstract
     */
    public function joinLeft($table, $cond, $cols = '*')
    {
        if (is_array($table)) {
            foreach ($table as $k => $v) {
                $alias = $k;
                $table = $v;
                break;
            }
        } else {
            $alias = $table;
        }

        if (!isset($this->_joinedTables[$alias])) {
            $this->getSelect()->joinLeft(
                array($alias => $this->getTable($table)),
                $cond,
                $cols
            );
            $this->_joinedTables[$alias] = true;
        }
        return $this;
    }

    /**
     * Get SQL for get record count
     *
     * NB: This varies from standard Magento in that is uses a sub-select if grouping is being used
     *
     * @return Varien_Db_Select
     */
    public function getSelectCountSql()
    {
        $this->_renderFilters();

        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);

        // Fix problem with mixing COUNT and GROUP BY or HAVING
        $groups = $countSelect->getPart(Zend_Db_Select::GROUP);
        $having = $countSelect->getPart(Zend_Db_Select::HAVING);
        if (count($groups) || count($having)) {
            $countSelect = $this->_conn->select()->from($countSelect, 'COUNT(*)');
        } else {
            $countSelect->reset(Zend_Db_Select::COLUMNS);
            $countSelect->columns('COUNT(*)');
        }

        return $countSelect;
    }

    /**
     * Add filter to Map
     *
     * @param string $filter
     * @param string $alias
     * @param string $group default 'fields'
     *
     * @return Varien_Data_Collection_Db
     */
    public function addFilterToMap($filter, $alias, $group = 'fields')
    {
        if (is_null($this->_map)) {
            $this->_map = array($group => array());
        } else {
            if (!isset($this->_map[$group])) {
                $this->_map[$group] = array();
            }
        }
        $this->_map[$group][$filter] = $alias;

        return $this;
    }

    public function addFilterToHavingMap($filter)
    {
        return $this->addFilterToMap($filter, true, 'having');
    }

    /**
     * Add field filter to collection
     *
     * @see self::_getConditionSql for $condition
     *
     * @param   string|array      $field
     * @param   null|string|array $condition
     * @param bool                $having
     *
     * @return  Mage_Eav_Model_Entity_Collection_Abstract
     */
    public function addFieldToFilter($field, $condition = null, $having = false)
    {
        $mapper = $this->_getMapper();
        if (!is_array($field)) {
            if (isset($mapper['having'][$field])) {
                $having = true;
            }
            $resultCondition = $this->_translateCondition($field, $condition);
        } else {
            $conditions = array();
            foreach ($field as $key => $currField) {
                if (isset($mapper['having'][$currField])) {
                    $having = true;
                }
                $conditions[] = $this->_translateCondition(
                    $currField,
                    isset($condition[$key]) ? $condition[$key] : null
                );
            }

            $resultCondition = '(' . join(') ' . Zend_Db_Select::SQL_OR . ' (', $conditions) . ')';
        }

        if ($having) {
            $this->_select->having($resultCondition);
        } else {
            $this->_select->where($resultCondition);
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function _toOptionArray($valueField = null, $labelField = 'name', $additional = array())
    {
        if ($valueField === null) {
            $valueField = $this->getResource()->getIdFieldName();
            if($valueField === null) {
                $valueField = 'id';
            }
        }

        return parent::_toOptionArray($valueField, $labelField, $additional);
    }

    /**
     * @inheritdoc
     */
    protected function _toOptionHash($valueField = null, $labelField = 'name')
    {
        if ($valueField === null) {
            $valueField = $this->getResource()->getIdFieldName();
            if($valueField === null) {
                $valueField = 'id';
            }
        }

        return parent::_toOptionHash($valueField, $labelField);
    }
}
