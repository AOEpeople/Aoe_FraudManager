<?php
/* @var $this Mage_Core_Model_Resource_Setup */
$this->startSetup();

$this->getConnection()->dropTable($this->getTable('Aoe_FraudManager/BlacklistRule'));
$this->getConnection()->dropTable($this->getTable('Aoe_FraudManager/HoldRule'));

$table = $this->getConnection()->newTable($this->getTable('Aoe_FraudManager/BlacklistRule'));
$table->addColumn(
    'id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'primary'  => true,
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
    )
);
$table->addColumn(
    'name',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    pow(2, 8) - 1,
    array('nullable' => false)
);
$table->addColumn(
    'description',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    pow(2, 16) - 1
);
$table->addColumn(
    'message',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    pow(2, 16) - 1
);
$table->addColumn(
    'stop_processing',
    Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    null,
    array('nullable' => false)
);
$table->addColumn(
    'is_active',
    Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    null,
    array('nullable' => false)
);
$table->addColumn(
    'conditions',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    pow(2, 32) - 1
);
$table->addColumn(
    'website_ids',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    pow(2, 16) - 1
);
$table->addColumn(
    'sort_order',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array('nullable' => false)
);
$table->addIndex(
    $this->getIdxName('Aoe_FraudManager/BlacklistRule', array('is_active')),
    array('is_active')
);
$this->getConnection()->createTable($table);

$table = $this->getConnection()->newTable($this->getTable('Aoe_FraudManager/HoldRule'));
$table->addColumn(
    'id',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array(
        'primary'  => true,
        'identity' => true,
        'unsigned' => true,
        'nullable' => false,
    )
);
$table->addColumn(
    'name',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    pow(2, 8) - 1,
    array('nullable' => false)
);
$table->addColumn(
    'description',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    pow(2, 16) - 1
);
$table->addColumn(
    'status',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    32,
    array('nullable' => true)
);
$table->addColumn(
    'stop_processing',
    Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    null,
    array('nullable' => false)
);
$table->addColumn(
    'is_active',
    Varien_Db_Ddl_Table::TYPE_BOOLEAN,
    null,
    array('nullable' => false)
);
$table->addColumn(
    'conditions',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    pow(2, 32) - 1
);
$table->addColumn(
    'website_ids',
    Varien_Db_Ddl_Table::TYPE_TEXT,
    pow(2, 16) - 1
);
$table->addColumn(
    'sort_order',
    Varien_Db_Ddl_Table::TYPE_INTEGER,
    null,
    array('nullable' => false)
);
$table->addIndex(
    $this->getIdxName('Aoe_FraudManager/HoldRule', array('is_active')),
    array('is_active')
);
$this->getConnection()->createTable($table);

$this->endSetup();
