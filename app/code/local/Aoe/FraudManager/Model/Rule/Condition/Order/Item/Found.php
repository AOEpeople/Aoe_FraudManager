<?php

class Aoe_FraudManager_Model_Rule_Condition_Order_Item_Found extends Aoe_FraudManager_Model_Rule_Condition_Order_Item_Combine
{
    /**
     * Internal constructor not depended on params. Can be used for object initialization
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setType('Aoe_FraudManager/Rule_Condition_Order_Item_Found');
        $this->setName('Product Attribute Combination');
    }


    /**
     * Load value options
     *
     * @return $this
     */
    public function getValueOptions()
    {
        return array(
            1 => Mage::helper('salesrule')->__('FOUND'),
            0 => Mage::helper('salesrule')->__('NOT FOUND'),
        );
    }

    public function getConditionConfigHtml()
    {
        $html = $this->translate(
            'If an item is %s in the order with %s of these conditions true:',
            $this->getValueElement()->getHtml(),
            $this->getAggregatorElement()->getHtml()
        );

        return $html;
    }

    /**
     * @param Varien_Object $object
     *
     * @return bool
     */
    public function validate(Varien_Object $object)
    {
        /** @var Mage_Sales_Model_Order $object */
        if (!$object instanceof Mage_Sales_Model_Order) {
            return false;
        }

        $conditions = $this->getConditions();
        if (empty($conditions)) {
            return true;
        }

        $all = ($this->getAggregator() === 'all');
        $true = (bool)$this->getValue();

        $found = false;
        foreach ($object->getAllItems() as $item) {
            /** @var Mage_Sales_Model_Order_Item $item */
            $found = $all;
            foreach ($conditions as $cond) {
                $validated = $cond->validate($item);
                if ($all !== $validated) {
                    $found = $validated;
                    break;
                }
            }

            if ($found) {
                break;
            }
        }

        // found an item and we're looking for existing one
        if ($found && $true) {
            return true;
        } // not found and we're making sure it doesn't exist
        elseif (!$found && !$true) {
            return true;
        }

        return false;
    }
}
