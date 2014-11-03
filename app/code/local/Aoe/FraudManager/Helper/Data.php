<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-03
 */
class Aoe_FraudManager_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isOrderFraud($order = null)
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && (bool)$order->getIsFraud()) {
            return true;
        }

        return false;
    }

    protected function resolveOrder($order = null)
    {
        if ($order instanceof Mage_Sales_Model_Order){
            return $order;
        } elseif($order) {
            return Mage::getModel('sales/order')->load($order);
        } else {
            return Mage::registry('current_order');
        }
    }
}
