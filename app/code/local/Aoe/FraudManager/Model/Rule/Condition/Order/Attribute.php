<?php

class Aoe_FraudManager_Model_Rule_Condition_Order_Attribute extends Aoe_FraudManager_Model_Rule_Condition_Attribute
{
    protected $attributes = [
        'grand_total'     => ['Grand Total', ['==', '!=', '<=', '<', '>=', '>']],
        'customer_email'  => ['Email Address', ['==', '!=', '{}', '!{}', 'RE']],
        'email_domain'    => ['Email Domain', ['()', '!()', 'RE']],
        'remote_ip'       => ['Remote IP', ['()', '!()']],
        'shipping_method' => ['Shipping Method', ['{}', '!{}', '()', '!()', 'RE']],
    ];

    protected $attributeOptions = null;

    /**
     * Internal constructor not depended on params. Can be used for object initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setType('Aoe_FraudManager/Rule_Condition_Order_Attribute');
        $this->setName('Order');
    }

    public function validate(Varien_Object $object)
    {
        if (!$object instanceof Mage_Sales_Model_Order) {
            if (is_callable([$object, 'getOrder'])) {
                $object = $object->getOrder();
                if (!$object instanceof Mage_Sales_Model_Order) {
                    return false;
                }
            } else {
                return false;
            }
        }

        return parent::validate($object);
    }

    protected function getAttributeValue(Varien_Object $object)
    {
        switch ($this->getAttribute()) {
            case 'email_domain':
                $emailAddress = $object->getDataUsingMethod('customer_email');
                $emailDomain = substr($emailAddress, strpos($emailAddress, '@') + 1);
                $emailDomain = trim(strtolower($emailDomain));

                return $emailDomain;
                break;
            case 'remote_ip':
                $ipList = explode(',', $object->getDataUsingMethod('x_forwarded_for'));
                $ipList[] = $object->getDataUsingMethod('remote_ip');
                $ipList = array_unique(array_filter(array_map('trim', $ipList)));

                return $ipList;
                break;
            default:
                return parent::getAttributeValue($object);
        }
    }
}
