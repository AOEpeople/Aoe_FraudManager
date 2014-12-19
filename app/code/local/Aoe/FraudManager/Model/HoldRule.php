<?php

class Aoe_FraudManager_Model_HoldRule extends Aoe_FraudManager_Model_Rule_Abstract
{
    protected function _construct()
    {
        $this->_setResourceModel('Aoe_FraudManager/HoldRule', 'Aoe_FraudManager/HoldRule_Collection');
    }
}
