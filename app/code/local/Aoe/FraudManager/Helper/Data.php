<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-03
 */
class Aoe_FraudManager_Helper_Data extends Aoe_Layout_Helper_Data
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
            return $this->getUrl('adminhtml/sales_order/setFraudFlag', array('order_id' => $order->getId()));
        }

        return false;
    }

    public function getRemoveFraudFlagUrl($order = null)
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            return $this->getUrl('adminhtml/sales_order/removeFraudFlag', array('order_id' => $order->getId()));
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

    /**
     * @return Varien_Data_Form
     */
    public function getRuleForm()
    {
        $form = new Varien_Data_Form(
            array(
                'id'            => 'edit_form',
                'method'        => 'post',
            )
        );

        $form->setUseContainer(true);

        return $form;
    }

    protected function resolveOrder($order = null)
    {
        if ($order instanceof Mage_Sales_Model_Order) {
            return $order;
        } elseif ($order) {
            return Mage::getModel('sales/order')->load($order);
        } else {
            return Mage::registry('current_order');
        }
    }

    /**
     * Retrieve url
     *
     * @param   string $route
     * @param   array  $params
     *
     * @return  string
     */
    protected function _getUrl($route, $params = array())
    {
        return Mage::helper('adminhtml/data')->getUrl($route, $params);
    }
}
