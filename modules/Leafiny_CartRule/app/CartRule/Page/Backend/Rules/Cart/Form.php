<?php
/**
 * This file is part of Leafiny.
 *
 * Copyright (C) Magentix SARL
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

/**
 * Class CartRule_Page_Backend_Rules_Cart_Form
 */
class CartRule_Page_Backend_Rules_Cart_Form extends Backend_Page_Admin_Form
{
    /**
     * Retrieve allowed cart rules
     *
     * @return string[]
     */
    public function getCartRuleTypes(?string $type = null): array
    {
        /** @var CartRule_Helper_Cart_Rule $helper */
        $helper = App::getSingleton('helper', 'cart_rule');

        $types = $helper->getCartRuleAllowedTypes();

        if ($type && isset($types[$type])) {
            return $types[$type];
        }

        return call_user_func_array('array_merge', $types);
    }

    /**
     * Retrieve conditions
     *
     * @return Leafiny_Object[]
     */
    public function getConditions(Leafiny_Object $rule): array
    {
        if (!is_string($rule->getData('conditions'))) {
            return [];
        }

        $data = json_decode($rule->getData('conditions'), true);
        if (!$data) {
            return [];
        }

        $conditions = [];

        foreach ($data as $condition) {
            if (!is_array($condition)) {
                continue;
            }
            $conditions[] = new Leafiny_Object($condition);
        }

        return $conditions;
    }

    /**
     * Retrieve all coupons for the rule
     *
     * @param int|null $ruleId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getCoupons(?int $ruleId): array
    {
        if (!$ruleId) {
            return [];
        }

        /** @var CartRule_Model_Cart_Rule_Coupon $couponModel */
        $couponModel = App::getSingleton('model', 'cart_rule_coupon');

        $filters = [
            [
                'column'   => 'rule_id',
                'value'    => $ruleId,
            ]
        ];

        return $couponModel->getList($filters);
    }
}