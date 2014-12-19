<?php

class Aoe_FraudManager_Model_BlacklistRule extends Aoe_FraudManager_Model_Rule_Abstract
{
    protected function _construct()
    {
        $this->_setResourceModel('Aoe_FraudManager/BlacklistRule', 'Aoe_FraudManager/BlacklistRule_Collection');
    }
}
