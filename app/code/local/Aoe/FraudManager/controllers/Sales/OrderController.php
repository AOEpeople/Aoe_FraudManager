<?php

/**
 * @see Mage_Adminhtml_Sales_OrderController
 */
class Aoe_FraudManager_Sales_OrderController extends Mage_Adminhtml_Controller_Action
{
    public function setFraudFlagAction()
    {
        $helper = $this->getHelper();
        $order = $this->initOrder();
        try {
            if ($this->getRequest()->isPost() || !$helper->isSetFlagCommentRequired($order)) {
                $comment = trim($this->getRequest()->getPost('comment'));
                if ($helper->setFlag($order, $comment)) {
                    $this->_getSession()->addSuccess($this->__('Marked order as fraud'));
                }
                $this->redirectToOrderView($order);
            } else {
                $this->loadLayout();
                $this->_setActiveMenu('sales/order');
                $this->_addBreadcrumb($this->__('Sales'), $this->__('Sales'));
                $this->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
                $this->renderLayout();
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->redirectToOrderView($order);
        }
    }

    public function removeFraudFlagAction()
    {
        $helper = $this->getHelper();
        $order = $this->initOrder();
        try {
            if ($this->getRequest()->isPost() || !$helper->isRemoveFlagCommentRequired($order)) {
                $comment = trim($this->getRequest()->getPost('comment'));
                if ($helper->removeFlag($order, $comment)) {
                    $this->_getSession()->addSuccess($this->__('Marked order as NOT fraud'));
                }
                $this->redirectToOrderView($order);
            } else {
                $this->loadLayout();
                $this->_setActiveMenu('sales/order');
                $this->_addBreadcrumb($this->__('Sales'), $this->__('Sales'));
                $this->_addBreadcrumb($this->__('Orders'), $this->__('Orders'));
                $this->renderLayout();
            }
        } catch (Exception $e) {
            Mage::logException($e);
            $this->_getSession()->addError($e->getMessage());
            $this->redirectToOrderView($order);
        }
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
        if ($order->isObjectNew()) {
            $e = new Mage_Core_Controller_Varien_Exception();
            throw $e->prepareForward('noroute');
        }

        $this->getHelper()->setCurrentOrder($order);

        return $order;
    }

    /**
     * Issue a redirect to the order view page
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return $this
     */
    protected function redirectToOrderView(Mage_Sales_Model_Order $order)
    {
        return $this->_redirect('adminhtml/sales_order/view', array('order_id' => $order->getId()));
    }

    /**
     * @return Aoe_FraudManager_Helper_FraudFlag
     */
    protected function getHelper()
    {
        return Mage::helper('Aoe_FraudManager/FraudFlag');
    }

    /**
     * ACL check
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        $order = $this->initOrder();
        $allowed = false;

        $action = lcfirst($this->getRequest()->getActionName());
        switch ($action) {
            case 'setFraudFlag':
                $allowed = $this->getHelper()->isSetFlagActionAllowed($order);
                break;
            case 'removeFraudFlag':
                $allowed = $this->getHelper()->isRemoveFlagActionAllowed($order);
                break;
        }

        return $allowed;
    }
}
