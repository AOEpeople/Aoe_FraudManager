<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-06
 */
class Aoe_FraudManager_FraudManager_HoldRuleController extends Aoe_FraudManager_Controller_RuleController
{
    /**
     * @return Aoe_FraudManager_Helper_HoldRule
     */
    protected function getHelper()
    {
        return Mage::helper('Aoe_FraudManager/HoldRule');
    }
}
