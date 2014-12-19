<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-06
 */
class Aoe_FraudManager_Helper_HoldRule extends Aoe_FraudManager_Helper_AbstractRule
{
    public function isActive()
    {
        return Mage::getStoreConfigFlag('aoe_fraudmanager/hold_rules/active');
    }

    /**
     * Get the frontname and controller portion of the route
     *
     * @return string
     */
    protected function getControllerRoute()
    {
        return 'adminhtml/fraudManager_holdRule';
    }

    /**
     * @param $action
     *
     * @return bool
     */
    public function getAclPermission($action)
    {
        return $this->getAdminSession()->isAllowed('sales/aoe_fraudmanager/hold_rule/' . trim($action, ' /'));
    }

    /**
     * @return string
     */
    public function getCurrentRecordKey()
    {
        return 'aoe_fraudmanager_hold_rule_CURRENT';
    }

    /**
     * Get a model instance
     *
     * @return Aoe_FraudManager_Model_HoldRule
     */
    public function getModel()
    {
        return Mage::getModel('Aoe_FraudManager/HoldRule');
    }

    /**
     * @return Varien_Data_Form
     */
    public function getMainForm($rule = null)
    {
        $form = parent::getMainForm($rule);

        /** @var Aoe_FraudManager_Model_HoldRule $rule */
        if (!$rule) {
            $rule = $this->getCurrentRecord();
        }

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $fieldset->addField(
            'status',
            'select',
            array(
                'label'    => $this->__('Status'),
                'title'    => $this->__('Status'),
                'name'     => 'status',
                'required' => true,
                'options'  => $this->getSourceModelHash('Aoe_FraudManager/Config_Source_Order_HoldStatuses'),
            ),
            'description'
        );

        $form->addValues($rule->getData());

        return $form;
    }
}
