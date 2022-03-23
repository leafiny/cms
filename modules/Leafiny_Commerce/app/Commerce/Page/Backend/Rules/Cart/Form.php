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
 * Class Commerce_Page_Backend_Rules_Cart_Form
 */
class Commerce_Page_Backend_Rules_Cart_Form extends Backend_Page_Admin_Form
{
    /**
     * Retrieve all coupons for the rule
     *
     * @param int|null $ruleId
     *
     * @return array
     * @throws Exception
     */
    public function getCoupons(?int $ruleId): array
    {
        if (!$ruleId) {
            return [];
        }

        /** @var Commerce_Model_Cart_Rule_Coupon $couponModel */
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