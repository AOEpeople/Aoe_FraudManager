<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-03
 *
 * @see    Mage_Adminhtml_Sales_OrderController
 */
class Aoe_FraudManager_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{
    public function setFraudFlagAction()
    {
        $order = $this->initOrder();

        if (Mage::helper('Aoe_FraudManager/Data')->setFraudFlag($order)) {
            $this->_getSession()->addSuccess($this->__('Marked order as fraud'));
        }

        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
    }

    public function removeFraudFlagAction()
    {
        $order = $this->initOrder();

        if (Mage::helper('Aoe_FraudManager/Data')->removeFraudFlag($order)) {
            $this->_getSession()->addSuccess($this->__('Marked order as NOT fraud'));
        }

        $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
    }

    /**
     * @return Mage_Sales_Model_Order
     *
     * @throws Mage_Core_Controller_Varien_Exception
     */
    protected function initOrder()
    {
        $id = $this->getRequest()->getParam('order', $this->getRequest()->getParam('order_id'));

        /** @var Mage_Sales_Model_Order $order */
        $order = Mage::getModel('sales/order')->load($id);
        if (!$order->getId()) {
            $e = new Mage_Core_Controller_Varien_Exception();
            throw $e->prepareForward('noroute');
        }

        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);

        return $order;
    }


    /**
     * Acl check for admin
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $action = strtolower($this->getRequest()->getActionName());
        switch ($action) {
            case 'setfraudflag':
                $aclResource = 'sales/order/actions/set_fraud_flag';
                break;
            case 'removefraudflag':
                $aclResource = 'sales/order/actions/remove_fraud_flag';
                break;
            default:
                return false;
        }

        return Mage::getSingleton('admin/session')->isAllowed($aclResource);
    }
}
