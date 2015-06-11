<?php

class Aoe_FraudManager_Helper_Condition extends Aoe_FraudManager_Helper_Data
{
    protected $operators;

    public function getConditionApplyImageUrl()
    {
        return Mage::getDesign()->getSkinUrl('images/rule_component_apply.gif');
    }

    public function getConditionApplyLabel()
    {
        return $this->__('Apply');
    }

    public function getOperators(array $filter = array())
    {
        if ($this->operators === null) {
            $this->operators = array(
                '=='  => $this->__('is'),
                '!='  => $this->__('is not'),
                '>='  => $this->__('equals or greater than'),
                '<='  => $this->__('equals or less than'),
                '>'   => $this->__('greater than'),
                '<'   => $this->__('less than'),
                '{}'  => $this->__('contains'),
                '!{}' => $this->__('does not contain'),
                '()'  => $this->__('is one of'),
                '!()' => $this->__('is not one of'),
                'RE'  => $this->__('matches regex pattern'),
            );
        }

        if (empty($filter)) {
            return $this->operators;
        } else {
            $operators = $this->operators;
            $operators = array_intersect_key($operators, array_flip($filter));
            return $operators;
        }
    }

    /**
     * @param $operator
     * @param $expectedValue
     * @param $actualValue
     *
     * @return bool
     */
    public function validateValue($operator, $expectedValue, $actualValue)
    {
        $result = false;
        $invertResult = false;

        switch ($operator) {
            case '!=':
                $invertResult = true;
                // Fall-through
            case '==':
                if (is_array($expectedValue)) {
                    if (is_array($actualValue)) {
                        $result = array_intersect($expectedValue, $actualValue);
                        $result = !empty($result);
                    } else {
                        return false;
                    }
                } else {
                    if (is_array($actualValue)) {
                        $result = count($actualValue) == 1 && array_shift($actualValue) == $expectedValue;
                    } else {
                        $result = $this->compareValues($actualValue, $expectedValue);
                    }
                }
                break;

            case '>':
                $invertResult = true;
                // Fall-through
            case '<=':
                if (!is_scalar($actualValue)) {
                    return false;
                }
                $result = $actualValue <= $expectedValue;
                break;

            case '<':
                $invertResult = true;
                // Fall-through
            case '>=':
                if (!is_scalar($actualValue)) {
                    return false;
                }
                $result = $actualValue >= $expectedValue;
                break;

            case '!{}':
                $invertResult = true;
                // Fall-through
            case '{}':
                if (is_array($expectedValue)) {
                    if (is_array($actualValue)) {
                        $result = array_intersect($expectedValue, $actualValue);
                        $result = !empty($result);
                    } elseif (is_scalar($actualValue)) {
                        foreach ($expectedValue as $item) {
                            if (stripos($actualValue, $item) !== false) {
                                $result = true;
                                break;
                            }
                        }
                    } else {
                        return false;
                    }
                } elseif (is_array($actualValue)) {
                    $result = in_array($expectedValue, $actualValue);
                } else {
                    $result = $this->compareValues($expectedValue, $actualValue, false);
                }
                break;

            case '!()':
                $invertResult = true;
                // Fall-through
            case '()':
                if (is_array($actualValue)) {
                    $result = (count(array_intersect($actualValue, (array)$expectedValue)) > 0);
                } else {
                    $expectedValue = (array)$expectedValue;
                    foreach ($expectedValue as $item) {
                        if ($this->compareValues($item, $actualValue)) {
                            $result = true;
                            break;
                        }
                    }
                }
                break;
            case 'RE':
                $result = preg_match($expectedValue, $actualValue);
                if ($result === false) {
                    Mage::throwException('Error running regex pattern (' . preg_last_error() . ').');
                }
                $result = (bool)$result;
                break;
        }

        if ($invertResult) {
            $result = !$result;
        }

        return $result;
    }

    /**
     * Case and type insensitive comparison of values
     *
     * @param  string|int|float $expectedValue
     * @param  string|int|float $actualValue
     *
     * @return bool
     */
    protected function compareValues($expectedValue, $actualValue, $strict = true)
    {
        if ($strict && is_numeric($expectedValue) && is_numeric($actualValue)) {
            return $expectedValue == $actualValue;
        } else {
            $validatePattern = preg_quote($expectedValue, '~');
            if ($strict) {
                $validatePattern = '^' . $validatePattern . '$';
            }
            return (bool)preg_match('~' . $validatePattern . '~iu', $actualValue);
        }
    }
}
