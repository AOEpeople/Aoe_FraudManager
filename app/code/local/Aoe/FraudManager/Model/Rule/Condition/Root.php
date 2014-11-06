<?php

class Aoe_FraudManager_Model_Rule_Condition_Root extends Aoe_FraudManager_Model_Rule_Condition_Combine
{
    /**
     * Internal constructor not depended on params. Can be used for object initialization
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setParentType($this->getType());

        $this->setId('root');
        $this->setType('Aoe_FraudManager/Rule_Condition_Root');
    }

    public function getSelfType()
    {
        return $this->getParentType();
    }

    //==================================================
    //=[ HTML Generation ]==============================
    //==================================================

    public function getRemoveLinkHtml()
    {
        return '';
    }
}
