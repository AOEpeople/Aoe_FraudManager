<?php

class Aoe_FraudManager_Resource_BlacklistRule extends Aoe_FraudManager_Resource_Abstract
{
    protected $_serializableFields = array(
        'website_ids' => array(
            '[]',
            array(),
            false,
            array('Aoe_FraudManager_Resource_BlacklistRule', 'implodeArray'),
            array('Aoe_FraudManager_Resource_BlacklistRule', 'explodeArray'),
        ),
        'conditions'  => array(
            '[]',
            array(),
            false,
            array('Zend_Json', 'encode'),
            array('Zend_Json', 'decode'),
        )
    );

    public function _construct()
    {
        $this->_init('Aoe_FraudManager/BlacklistRule', 'id');
    }
}
