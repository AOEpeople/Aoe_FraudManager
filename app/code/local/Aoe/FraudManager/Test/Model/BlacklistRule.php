<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2015-08-06
 */
class Aoe_FraudManager_Test_Model_BlacklistRule extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function getModel()
    {
        /** @var Aoe_FraudManager_Model_BlacklistRule $model */
        $model = Mage::getModel('Aoe_FraudManager/BlacklistRule');
        $this->assertInstanceOf('Aoe_FraudManager_Model_BlacklistRule', $model);
    }

    /**
     * @test
     *
     * @depends getModel
     *
     * @param Aoe_FraudManager_Model_BlacklistRule $model
     */
    public function getModelResource(Aoe_FraudManager_Model_BlacklistRule $model)
    {
        /** @var Aoe_FraudManager_Resource_BlacklistRule $resource */
        $resource = $model->getResource();
        $this->assertInstanceOf('Aoe_FraudManager_Resource_BlacklistRule', $resource);

        /** @var Aoe_FraudManager_Resource_BlacklistRule $resource */
        $resource = Mage::getResourceModel('Aoe_FraudManager/BlacklistRule');
        $this->assertInstanceOf('Aoe_FraudManager_Resource_BlacklistRule', $resource);
    }

    /**
     * @test
     *
     * @depends getModel
     *
     * @param Aoe_FraudManager_Model_BlacklistRule $model
     */
    public function getModelCollection(Aoe_FraudManager_Model_BlacklistRule $model)
    {
        /** @var Aoe_FraudManager_Resource_BlacklistRule_Collection $collection */
        $collection = $model->getResourceCollection();
        $this->assertInstanceOf('Aoe_FraudManager_Resource_BlacklistRule_Collection', $collection);
    }
}
