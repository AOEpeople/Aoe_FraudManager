<?php

class Aoe_FraudManager_FraudManager_BlacklistRuleController extends Aoe_FraudManager_Controller_RuleController
{
    /**
     * @return Aoe_FraudManager_Helper_BlacklistRule
     */
    protected function getHelper()
    {
        return Mage::helper('Aoe_FraudManager/BlacklistRule');
    }
}
