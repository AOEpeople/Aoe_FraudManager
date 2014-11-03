<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-03
 */
class Aoe_FraudManager_FraudController extends Mage_Adminhtml_Controller_Action
{
    public function setAction()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order'));
        if (!$order->getId()) {
            $this->_forward('noroute');
        }

        if (Mage::helper('Aoe_FraudManager/Data')->setFraudFlag($order)) {
            $this->_getSession()->addSuccess($this->__('Marked order as fraud'));
        }

        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
    }

    public function removeAction()
    {
        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order'));
        if (!$order->getId()) {
            $this->_forward('noroute');
        }

        if (Mage::helper('Aoe_FraudManager/Data')->removeFraudFlag($order)) {
            $this->_getSession()->addSuccess($this->__('Marked order as NOT fraud'));
        }

        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
    }
}
