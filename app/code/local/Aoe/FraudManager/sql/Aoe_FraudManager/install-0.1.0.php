<?php
$setupConfig = Mage::getConfig()->getNode('global/resources/sales_setup/setup');
$setupClass = $setupConfig->getClassName();
$setupClass = ($setupClass ? $setupClass : 'Mage_Sales_Model_Resource_Setup');
$setup = new $setupClass('sales_setup');
/* @var Mage_Sales_Model_Resource_Setup $setup */

$setup->startSetup();

$setup->addAttribute(
    Mage_Sales_Model_Order::ENTITY,
    'is_fraud',
    array(
        'type'     => Varien_Db_Ddl_Table::TYPE_BOOLEAN,
        'required' => true,
        'comment'  => 'Fraud flag',
        'grid'     => true,
    )
);

$setup->endSetup();
