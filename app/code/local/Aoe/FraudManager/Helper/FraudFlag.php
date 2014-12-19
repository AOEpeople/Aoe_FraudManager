<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-12-19
 */
class Aoe_FraudManager_Helper_FraudFlag extends Aoe_FraudManager_Helper_Data
{
    public function isActive()
    {
        return Mage::getStoreConfigFlag('aoe_fraudmanager/fraud_flag/active', Mage_Core_Model_Store::ADMIN_CODE);
    }

    public function isSetFlagCommentRequired()
    {
        return Mage::getStoreConfigFlag('aoe_fraudmanager/fraud_flag/comment_required', Mage_Core_Model_Store::ADMIN_CODE);
    }

    public function isRemoveFlagCommentRequired()
    {
        return Mage::getStoreConfigFlag('aoe_fraudmanager/fraud_flag/comment_required', Mage_Core_Model_Store::ADMIN_CODE);
    }

    public function isFlagged($order = null)
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && (bool)$order->getIsFraud()) {
            return true;
        }

        return false;
    }

    public function isSetFlagActionAllowed($order = null)
    {
        if (!$this->isActive()) {
            return false;
        }

        $order = $this->resolveOrder($order);

        if (!$order instanceof Mage_Sales_Model_Order || $order->isObjectNew()) {
            return false;
        }

        if ($this->isFlagged($order)) {
            return false;
        }

        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/set_fraud_flag');
    }

    public function isRemoveFlagActionAllowed($order = null)
    {
        if (!$this->isActive()) {
            return false;
        }

        $order = $this->resolveOrder($order);

        if (!$order instanceof Mage_Sales_Model_Order || $order->isObjectNew()) {
            return false;
        }

        if (!$this->isFlagged($order)) {
            return false;
        }

        return Mage::getSingleton('admin/session')->isAllowed('sales/order/actions/remove_fraud_flag');
    }

    public function getSetFlagUrl($order = null)
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            return $this->getUrl('adminhtml/sales_order/setFraudFlag', array('order_id' => $order->getId()));
        }

        return false;
    }

    public function getRemoveFlagUrl($order = null)
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            return $this->getUrl('adminhtml/sales_order/removeFraudFlag', array('order_id' => $order->getId()));
        }

        return false;
    }

    public function setFlag($order = null, $message = '')
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

    public function removeFlag($order = null, $message = '')
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

    public function getCommentForm($order = null)
    {
        $order = $this->resolveOrder($order);
        if (!$order instanceof Mage_Sales_Model_Order) {
            return null;
        }

        $form = new Varien_Data_Form();
        $form->setId('edit_form');
        $form->setMethod('post');
        $form->setUseContainer(true);

        $fieldset = $form->addFieldset('base_fieldset', array('legend' => $this->__('Order')));

        $fieldset->addField(
            'comment',
            'textarea',
            array(
                'id'       => 'comment',
                'name'     => 'comment',
                'label'    => $this->__('Comment'),
                'class'    => 'required-entry',
                'required' => true,
            )
        );

        return $form;
    }

    /**
     * Return the current order or an empty order
     *
     * @return Mage_Sales_Model_Order
     */
    public function getCurrentOrder()
    {
        $order = Mage::registry('current_order');
        if ($order instanceof Mage_Sales_Model_Order) {
            return $order;
        } else {
            Mage::getModel('sales/order');
        }
    }

    /**
     *
     * @param Mage_Sales_Model_Order $order
     *
     * @return $this
     */
    public function setCurrentOrder(Mage_Sales_Model_Order $order = null)
    {
        Mage::unregister('sales_order');
        Mage::unregister('current_order');

        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);

        return $this;
    }

    protected function resolveOrder($order = null)
    {
        if ($order instanceof Mage_Sales_Model_Order) {
            return $order;
        } elseif ($order) {
            return Mage::getModel('sales/order')->load($order);
        } else {
            return $this->getCurrentOrder();
        }
    }
}
