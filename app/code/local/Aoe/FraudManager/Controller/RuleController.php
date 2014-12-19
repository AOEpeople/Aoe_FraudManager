<?php

abstract class Aoe_FraudManager_Controller_RuleController extends Aoe_Layout_Controller_ModelManager
{
    public function conditionAction()
    {
        if (!$this->getRequest()->isAjax()) {
            $this->_forward('noroute');
            return;
        }

        $id = $this->getRequest()->getParam('id');
        $type = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));

        /** @var Aoe_FraudManager_Model_Rule_Condition_Interface $condition */
        $condition = Mage::getModel($type[0]);
        if (!$condition instanceof Aoe_FraudManager_Model_Rule_Condition_Interface) {
            $this->_forward('noroute');
            return;
        }

        /** @var Aoe_FraudManager_Model_Rule_Abstract $rule */
        $rule = $this->getHelper()->getModel();

        $condition->setId($id);
        $condition->setRule($rule);
        if (is_callable(array($condition, 'setJsFormObject'))) {
            $condition->setJsFormObject($this->getRequest()->getParam('form'));
        }
        if (isset($type[1]) && is_callable(array($condition, 'setAttribute'))) {
            $condition->setAttribute($type[1]);
        }

        $this->getResponse()->setBody($condition->getHtml());
    }

    /**
     * Pre-process the POST data before adding to the model
     *
     * @param array $postData
     *
     * @return array
     */
    protected function preprocessPostData(array $postData)
    {
        $postData = parent::preprocessPostData($postData);

        if (isset($postData['rule']) && is_array($postData['rule'])) {
            $rule = $this->getHelper()->convertFlatToRecursive($postData['rule'], array('conditions'));
            unset($postData['rule']);
            if (isset($rule['conditions'])) {
                $postData['conditions'] = reset($rule['conditions']);
            }
        }

        return $postData;
    }
}
