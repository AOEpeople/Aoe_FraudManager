<?php

class Aoe_FraudManager_Resource_HoldRule_Collection extends Aoe_FraudManager_Resource_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('Aoe_FraudManager/HoldRule');
    }

    public function filterValidForOrder(Mage_Sales_Model_Order $order, $includeInactive = false)
    {
        if(!$includeInactive) {
            $this->addFieldToFilter('is_active', '1');
        }
        $this->addFieldToFilter('website_ids', ['finset' => $order->getStore()->getWebsiteId()]);
        $this->addOrder('sort_order', 'DESC');

        return $this;
    }
}
