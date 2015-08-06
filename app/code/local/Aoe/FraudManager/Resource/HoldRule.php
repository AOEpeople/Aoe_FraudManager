<?php

class Aoe_FraudManager_Resource_HoldRule extends Aoe_FraudManager_Resource_Abstract
{
    protected $_serializableFields = [
        'website_ids' => [
            '[]',
            [],
            false,
            ['Aoe_FraudManager_Resource_HoldRule', 'implodeArray'],
            ['Aoe_FraudManager_Resource_HoldRule', 'explodeArray'],
        ],
        'conditions'  => [
            '[]',
            [],
            false,
            ['Zend_Json', 'encode'],
            ['Zend_Json', 'decode'],
        ],
    ];

    public function _construct()
    {
        $this->_init('Aoe_FraudManager/HoldRule', 'id');
    }
}
