<?php

class Aoe_FraudManager_Helper_BlacklistRule extends Aoe_FraudManager_Helper_AbstractRule
{
    const XML_PATH_NOTIFICATION_EMAIL_TEMPLATE = 'aoe_fraudmanager/blacklist_rules/notification_email_template';
    const XML_PATH_NOTIFICATION_EMAIL_SENDER = 'aoe_fraudmanager/blacklist_rules/notification_email_sender';
    const XML_PATH_NOTIFICATION_EMAIL_RECEIVER = 'aoe_fraudmanager/blacklist_rules/notification_email_receiver';

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
        return $this->getAdminSession()->isAllowed('system/aoe_fraudmanager/blacklist_rule/' . trim($action, ' /'));
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

    /**
     * Email notification for blacklist activation
     *
     * @param string          $message
     * @param array           $extraVariables
     * @param int|string|null $store
     *
     * @return $this
     */
    public function notify($message, array $extraVariables = array(), $store = null)
    {
        $store = Mage::app()->getStore($store ? $store : Mage_Core_Model_Store::ADMIN_CODE);

        // Sender identity code
        $sender = Mage::getStoreConfig(self::XML_PATH_NOTIFICATION_EMAIL_SENDER, $store);
        if (!$sender) {
            return $this;
        }

        // Receiver identity code
        $receiver = Mage::getStoreConfig(self::XML_PATH_NOTIFICATION_EMAIL_RECEIVER, $store);
        if (!$receiver) {
            return $this;
        }

        // Template ID
        $templateId = Mage::getStoreConfig(self::XML_PATH_NOTIFICATION_EMAIL_TEMPLATE, $store);
        if (!$templateId) {
            return $this;
        }

        /** @var Mage_Core_Model_Email_Info $emailInfo */
        $emailInfo = Mage::getModel('core/email_info');
        $emailInfo->addTo(
            Mage::getStoreConfig("trans_email/ident_{$receiver}/email", $store),
            Mage::getStoreConfig("trans_email/ident_{$receiver}/name", $store)
        );

        /** @var $mailer Mage_Core_Model_Email_Template_Mailer */
        $mailer = Mage::getModel('core/email_template_mailer');
        $mailer->addEmailInfo($emailInfo);

        // Set all required params and send emails
        $mailer->setStoreId($store->getId());
        $mailer->setSender($sender);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams($extraVariables + array('message' => $message));
        $mailer->send();

        return $this;
    }
}
