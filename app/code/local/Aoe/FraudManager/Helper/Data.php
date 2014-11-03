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

    public function getSetFraudFlagUrl($order = null)
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            return $this->_getUrl('adminhtml/fraud/set', array('order' => $order->getId()));
        }

        return false;
    }

    public function getRemoveFraudFlagUrl($order = null)
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            return $this->_getUrl('adminhtml/fraud/remove', array('order' => $order->getId()));
        }

        return false;
    }

    public function setFraudFlag($order = null, $message = '')
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId() && !$order->getIsFraud()) {
            $order->setIsFraud(1);
            $order->addStatusHistoryComment(trim($this->__('Marked order as fraud') . "\n\n" . $message));
            $order->save();
            return true;
        }

        return false;
    }

    public function removeFraudFlag($order = null, $message = '')
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId() && $order->getIsFraud()) {
            $order->setIsFraud(0);
            $order->addStatusHistoryComment(trim($this->__('Marked order as NOT fraud') . "\n\n" . $message));
            $order->save();
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

    /**
     * Retrieve url
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    protected function _getUrl($route, $params = array())
    {
        return $this->getUrl($route, $params);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route='', $params=array())
    {
        return Mage::helper('adminhtml')->getUrl($route, $params);
    }
}
