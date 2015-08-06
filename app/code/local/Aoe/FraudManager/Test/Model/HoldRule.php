<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2015-08-06
 */
class Aoe_FraudManager_Test_Model_HoldRule extends EcomDev_PHPUnit_Test_Case
{
    /**
     * @test
     */
    public function getModel()
    {
        /** @var Aoe_FraudManager_Model_HoldRule $model */
        $model = Mage::getModel('Aoe_FraudManager/HoldRule');
        $this->assertInstanceOf('Aoe_FraudManager_Model_HoldRule', $model);
    }

    /**
     * @test
     *
     * @depends getModel
     *
     * @param Aoe_FraudManager_Model_HoldRule $model
     */
    public function getModelResource(Aoe_FraudManager_Model_HoldRule $model)
    {
        /** @var Aoe_FraudManager_Resource_HoldRule $resource */
        $resource = $model->getResource();
        $this->assertInstanceOf('Aoe_FraudManager_Resource_HoldRule', $resource);

        /** @var Aoe_FraudManager_Resource_HoldRule $resource */
        $resource = Mage::getResourceModel('Aoe_FraudManager/HoldRule');
        $this->assertInstanceOf('Aoe_FraudManager_Resource_HoldRule', $resource);
    }

    /**
     * @test
     *
     * @depends getModel
     *
     * @param Aoe_FraudManager_Model_HoldRule $model
     */
    public function getModelCollection(Aoe_FraudManager_Model_HoldRule $model)
    {
        /** @var Aoe_FraudManager_Resource_HoldRule_Collection $collection */
        $collection = $model->getResourceCollection();
        $this->assertInstanceOf('Aoe_FraudManager_Resource_HoldRule_Collection', $collection);
    }
}
