<?php

class Aoe_FraudManager_Helper_FraudFlag extends Aoe_FraudManager_Helper_Data
{
    const XML_PATH_ACTIVE = 'aoe_fraudmanager/fraud_flag/active';
    const XML_PATH_COMMENT_REQUIRED = 'aoe_fraudmanager/fraud_flag/comment_required';
    const XML_PATH_AUTO_HOLD_STATUS = 'aoe_fraudmanager/fraud_flag/auto_hold_status';
    const ACL_PREFIX = 'sales/order/actions/';

    public function isActive($store = null)
    {
        return Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE, $store);
    }

    public function isSetFlagCommentRequired($order = null)
    {
        $order = $this->resolveOrder($order);
        if (!$order instanceof Mage_Sales_Model_Order || $order->isObjectNew()) {
            return false;
        }

        if (!$this->isActive($order->getStoreId())) {
            return false;
        }

        return Mage::getStoreConfigFlag(self::XML_PATH_COMMENT_REQUIRED, $order->getStoreId());
    }

    public function isRemoveFlagCommentRequired($order = null)
    {
        $order = $this->resolveOrder($order);
        if (!$order instanceof Mage_Sales_Model_Order || $order->isObjectNew()) {
            return false;
        }

        if (!$this->isActive($order->getStoreId())) {
            return false;
        }

        return Mage::getStoreConfigFlag(self::XML_PATH_COMMENT_REQUIRED, $order->getStoreId());
    }

    public function getAutoHoldStatus($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_AUTO_HOLD_STATUS, $store);
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
        $order = $this->resolveOrder($order);
        if (!$order instanceof Mage_Sales_Model_Order || $order->isObjectNew()) {
            return false;
        }

        if (!$this->isActive($order->getStoreId())) {
            return false;
        }

        if ($this->isFlagged($order)) {
            return false;
        }

        return $this->getAdminSession()->isAllowed(self::ACL_PREFIX . 'set_fraud_flag');
    }

    public function isRemoveFlagActionAllowed($order = null)
    {
        $order = $this->resolveOrder($order);
        if (!$order instanceof Mage_Sales_Model_Order || $order->isObjectNew()) {
            return false;
        }

        if (!$this->isActive($order->getStoreId())) {
            return false;
        }

        if (!$this->isFlagged($order)) {
            return false;
        }

        return $this->getAdminSession()->isAllowed(self::ACL_PREFIX . 'remove_fraud_flag');
    }

    public function getSetFlagUrl($order = null)
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            return $this->getUrl('adminhtml/sales_order/setFraudFlag', ['order_id' => $order->getId()]);
        }

        return false;
    }

    public function getRemoveFlagUrl($order = null)
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId()) {
            return $this->getUrl('adminhtml/sales_order/removeFraudFlag', ['order_id' => $order->getId()]);
        }

        return false;
    }

    public function setFlag($order = null, $message = '')
    {
        $order = $this->resolveOrder($order);

        if ($order instanceof Mage_Sales_Model_Order && $order->getId() && !$order->getIsFraud()) {
            $order->setIsFraud(1);
            $comment = trim($this->__('Marked order as fraud') . "\n\n" . $message);
            $holdStatus = $this->getAutoHoldStatus($order->getStore());
            if ($holdStatus && $order->canHold()) {
                $order->setHoldBeforeState($order->getState());
                $order->setHoldBeforeStatus($order->getStatus());
                $order->setState(Mage_Sales_Model_Order::STATE_HOLDED, $holdStatus, $comment);
            } else {
                $order->addStatusHistoryComment($comment);
            }
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
            $comment = trim($this->__('Marked order as NOT fraud') . "\n\n" . $message);
            $holdStatus = $this->getAutoHoldStatus($order->getStore());
            if ($holdStatus && $order->canUnhold() && $order->getStatus() === $holdStatus) {
                $order->setState($order->getHoldBeforeState(), $order->getHoldBeforeStatus(), $comment);
                $order->setHoldBeforeState(null);
                $order->setHoldBeforeStatus(null);
            } else {
                $order->addStatusHistoryComment($comment);
            }
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

        $fieldset = $form->addFieldset('base_fieldset', ['legend' => $this->__('Order')]);

        $fieldset->addField(
            'comment',
            'textarea',
            [
                'id'       => 'comment',
                'name'     => 'comment',
                'label'    => $this->__('Comment'),
                'class'    => 'required-entry',
                'required' => true,
            ]
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
