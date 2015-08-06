<?php

abstract class Aoe_FraudManager_Helper_AbstractRule extends Aoe_Layout_Helper_AbstractModelManager
{
    public function getGridCollection()
    {
        return $this->getCollection();
    }

    /**
     * @return Varien_Data_Form
     */
    public function getForm($rule = null)
    {
        /** @var Aoe_FraudManager_Model_Rule_Abstract $rule */
        if (!$rule) {
            $rule = $this->getCurrentRecord();
        }

        $form = new Varien_Data_Form(
            [
                'id'            => 'edit_form',
                'action'        => $this->getEditUrl($rule),
                'method'        => 'post',
                'use_container' => true,
            ]
        );

        return $form;
    }

    /**
     * @return Varien_Data_Form
     */
    public function getMainForm($rule = null)
    {
        /** @var Aoe_FraudManager_Model_Rule_Abstract $rule */
        if (!$rule) {
            $rule = $this->getCurrentRecord();
        }

        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => $this->__('General Information')]
        );

        $fieldset->addField(
            'name',
            'text',
            [
                'name'     => 'name',
                'label'    => $this->__('Rule Name'),
                'title'    => $this->__('Rule Name'),
                'required' => true,
            ]
        );

        $fieldset->addField(
            'description',
            'textarea',
            [
                'name'  => 'description',
                'label' => $this->__('Description'),
                'title' => $this->__('Description'),
                'style' => 'height: 100px;',
            ]
        );

        $fieldset->addField(
            'stop_processing',
            'select',
            [
                'name'     => 'stop_processing',
                'label'    => $this->__('Stop Processing'),
                'title'    => $this->__('Stop Processing'),
                'comment'  => $this->__('Stop further rules processing is this rule matches'),
                'required' => true,
                'options'  => $this->getSourceModelHash('adminhtml/system_config_source_yesno'),
            ]
        );

        $fieldset->addField(
            'is_active',
            'select',
            [
                'name'     => 'is_active',
                'label'    => $this->__('Active'),
                'title'    => $this->__('Active'),
                'required' => true,
                'options'  => $this->getSourceModelHash('adminhtml/system_config_source_yesno'),
            ]
        );

        if ($this->getIsSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField(
                'website_ids',
                'hidden',
                [
                    'name'  => 'website_ids[]',
                    'value' => $websiteId,
                ]
            );
            $rule->setWebsiteIds($websiteId);
        } else {
            $fieldset->addField(
                'website_ids',
                'multiselect',
                [
                    'name'     => 'website_ids[]',
                    'label'    => $this->__('Websites'),
                    'title'    => $this->__('Websites'),
                    'required' => true,
                    'values'   => $this->getSourceModelArray('core/website', true),
                ]
            );
        }

        $fieldset->addField(
            'sort_order',
            'text',
            [
                'name'    => 'sort_order',
                'label'   => $this->__('Sort Order'),
                'comment' => $this->__('High numbers are processed first'),
            ]
        );

        $form->addValues($rule->getData());

        return $form;
    }

    /**
     * @return Varien_Data_Form
     */
    public function getConditionsForm($rule = null)
    {
        /** @var Aoe_FraudManager_Model_Rule_Abstract $rule */
        if (!$rule) {
            $rule = $this->getCurrentRecord();
        }

        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $fieldset = $form->addFieldset('conditions_fieldset', ['legend' => $this->__('Apply the rule only if the following conditions are met')]);

        $renderer = clone $form->getFieldsetRenderer();
        $renderer->setTemplate('promo/fieldset.phtml');
        $renderer->setNewChildUrl($this->getUrl('*/*/condition', ['form' => $fieldset->getHtmlId()]));
        $fieldset->setRenderer($renderer);

        $conditionsElement = $fieldset->addField(
            'conditions',
            'text',
            [
                'name'  => 'conditions',
                'label' => $this->__('Conditions'),
                'title' => $this->__('Conditions'),
            ]
        );
        $conditionsElement->setRenderer(Mage::getSingleton('Aoe_FraudManager/Form_Element_Renderer_Conditions'));

        $form->addValues(['conditions' => $rule->getConditions()]);

        return $form;
    }

    /**
     * @param array $original
     *
     * @return array
     */
    public function convertFlatToRecursive(array $original, array $keys)
    {
        $result = [];

        foreach ($original as $key => $value) {
            if (in_array($key, $keys) && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node =& $result;
                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = [];
                        }
                        $node =& $node[$key][$path[$i]];
                    }
                    foreach ($data as $k => $v) {
                        $node[$k] = $v;
                    }
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }
}
