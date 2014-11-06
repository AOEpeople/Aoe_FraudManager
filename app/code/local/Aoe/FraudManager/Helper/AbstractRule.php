<?php

/**
 * @author Lee Saferite <lee.saferite@aoe.com>
 * @since  2014-11-06
 */
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
            array(
                'id'            => 'edit_form',
                'action'        => $this->getEditUrl($rule),
                'method'        => 'post',
                'use_container' => true,
            )
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
            array('legend' => $this->__('General Information'))
        );

        $fieldset->addField(
            'name',
            'text',
            array(
                'name'     => 'name',
                'label'    => $this->__('Rule Name'),
                'title'    => $this->__('Rule Name'),
                'required' => true,
            )
        );

        $fieldset->addField(
            'description',
            'textarea',
            array(
                'name'  => 'description',
                'label' => $this->__('Description'),
                'title' => $this->__('Description'),
                'style' => 'height: 100px;',
            )
        );

        $fieldset->addField(
            'stop_processing',
            'select',
            array(
                'name'     => 'stop_processing',
                'label'    => $this->__('Stop Processing'),
                'title'    => $this->__('Stop Processing'),
                'comment'  => $this->__('Stop further rules processing is this rule matches'),
                'required' => true,
                'options'  => $this->getSourceModelHash('adminhtml/system_config_source_yesno')
            )
        );

        $fieldset->addField(
            'is_active',
            'select',
            array(
                'name'     => 'is_active',
                'label'    => $this->__('Active'),
                'title'    => $this->__('Active'),
                'required' => true,
                'options'  => $this->getSourceModelHash('adminhtml/system_config_source_yesno')
            )
        );

        if ($this->getIsSingleStoreMode()) {
            $websiteId = Mage::app()->getStore(true)->getWebsiteId();
            $fieldset->addField(
                'website_ids',
                'hidden',
                array(
                    'name'  => 'website_ids[]',
                    'value' => $websiteId
                )
            );
            $rule->setWebsiteIds($websiteId);
        } else {
            $fieldset->addField(
                'website_ids',
                'multiselect',
                array(
                    'name'     => 'website_ids[]',
                    'label'    => $this->__('Websites'),
                    'title'    => $this->__('Websites'),
                    'required' => true,
                    'values'   => $this->getSourceModelArray('core/website', true)
                )
            );
        }

        $fieldset->addField(
            'sort_order',
            'text',
            array(
                'name'    => 'sort_order',
                'label'   => $this->__('Sort Order'),
                'comment' => $this->__('High numbers are processed first')
            )
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

        $fieldset = $form->addFieldset('conditions_fieldset', array('legend' => $this->__('Apply the rule only if the following conditions are met')));

        $renderer = clone $form->getFieldsetRenderer();
        $renderer->setTemplate('promo/fieldset.phtml');
        $renderer->setNewChildUrl($this->getUrl('*/*/condition', array('form' => $fieldset->getHtmlId())));
        $fieldset->setRenderer($renderer);

        $conditionsElement = $fieldset->addField(
            'conditions',
            'text',
            array(
                'name'  => 'conditions',
                'label' => $this->__('Conditions'),
                'title' => $this->__('Conditions'),
            )
        );
        $conditionsElement->setRenderer(Mage::getSingleton('Aoe_FraudManager/Form_Element_Renderer_Conditions'));

        $form->addValues(array('conditions' => $rule->getConditions()));

        return $form;
    }

    /**
     * @param array $original
     *
     * @return array
     */
    public function convertFlatToRecursive(array $original, array $keys)
    {
        $result = array();

        foreach ($original as $key => $value) {
            if (in_array($key, $keys) && is_array($value)) {
                foreach ($value as $id => $data) {
                    $path = explode('--', $id);
                    $node =& $result;
                    for ($i = 0, $l = sizeof($path); $i < $l; $i++) {
                        if (!isset($node[$key][$path[$i]])) {
                            $node[$key][$path[$i]] = array();
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

    /**
     * Retrieve url
     *
     * @param   string $route
     * @param   array  $params
     *
     * @return  string
     */
    protected function _getUrl($route, $params = array())
    {
        return $this->getUrl($route, $params);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array  $params
     *
     * @return  string
     */
    public function getUrl($route = '', $params = array())
    {
        return Mage::helper('adminhtml')->getUrl($route, $params);
    }
}
