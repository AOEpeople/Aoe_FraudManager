<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-06
 */
class Aoe_FraudManager_Helper_BlacklistRule extends Aoe_FraudManager_Helper_AbstractRule
{
    public function isActive()
    {
        return Mage::getStoreConfigFlag('aoe_fraudmanager/blacklist_rules/active');
    }

    /**
     * Get the frontname and controller portion of the route
     *
     * @return string
     */
    protected function getControllerRoute()
    {
        return 'adminhtml/fraudManager_blacklistRule';
    }

    /**
     * @param $action
     *
     * @return bool
     */
    public function getAclPermission($action)
    {
        return $this->getAdminSession()->isAllowed('sales/aoe_fraudmanager/blacklist_rule/' . trim($action, ' /'));
    }

    /**
     * @return string
     */
    public function getCurrentRecordKey()
    {
        return 'aoe_fraudmanager_blacklist_rule_CURRENT';
    }

    /**
     * Get a model instance
     *
     * @return Aoe_FraudManager_Model_BlacklistRule
     */
    public function getModel()
    {
        return Mage::getModel('Aoe_FraudManager/BlacklistRule');
    }

    /**
     * @return Varien_Data_Form
     */
    public function getMainForm($rule = null)
    {
        $form = parent::getMainForm($rule);

        /** @var Aoe_FraudManager_Model_BlacklistRule $rule */
        if (!$rule) {
            $rule = $this->getCurrentRecord();
        }

        /** @var Varien_Data_Form_Element_Fieldset $fieldset */
        $fieldset = $form->getElement('base_fieldset');

        $fieldset->addField(
            'message',
            'textarea',
            array(
                'name'     => 'message',
                'label'    => $this->__('Message'),
                'title'    => $this->__('Message'),
                'comment'  => $this->__('Message sent to customer when this rule is activated'),
                'style'    => 'height: 100px;',
                'required' => false,
            ),
            'description'
        );

        $form->addValues($rule->getData());

        return $form;
    }
}
