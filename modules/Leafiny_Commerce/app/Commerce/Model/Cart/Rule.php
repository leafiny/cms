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
 * Class Commerce_Model_Cart_Rule
 */
class Commerce_Model_Cart_Rule extends Core_Model
{
    public const TYPE_PERCENT_SHIPPING = 'percent_shipping';
    public const TYPE_PERCENT_SUBTOTAL = 'percent_subtotal';
    public const TYPE_AMOUNT_PER_PRODUCT  = 'amount_per_product';

    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_cart_rule';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'rule_id';

    /**
     * Save
     *
     * @param Leafiny_Object $object
     *
     * @return int|null
     * @throws Exception
     */
    public function save(Leafiny_Object $object): ?int
    {
        $ruleId = parent::save($object);

        /** @var Commerce_Model_Cart_Rule_Coupon $couponModel */
        $couponModel = App::getObject('model', 'cart_rule_coupon');

        if ($ruleId && !$object->getData('has_coupon')) {
            $couponModel->deleteAll($ruleId);
        }

        if ($ruleId && $object->getData('coupons')) {
            $coupons = $object->getData('coupons')->getData();

            foreach($coupons as $coupon) {
                $coupon = New Leafiny_Object($coupon);
                if ($coupon->getData('delete') && $coupon->getData('coupon_id')) {
                    $couponModel->delete((int)$coupon->getData('coupon_id'));
                }

                if (!$coupon->getData('delete') && $coupon->getData('code')) {
                    $couponModel->save(
                        new Leafiny_Object(
                            [
                                'coupon_id' => $coupon->getData('coupon_id') ?: null,
                                'rule_id'   => $ruleId,
                                'code'      => $coupon->getData('code'),
                                'limit'     => $coupon->getData('limit') ?: 1,
                                'status'    => $coupon->getData('status') ?: 1,
                            ]
                        )
                    );
                }
            }
        }

        return $ruleId;
    }

    /**
     * Check date is expired
     *
     * @param string|null $date
     *
     * @return bool
     */
    public function isExpired(?string $date): bool
    {
        if (!$date) {
            return false;
        }

        return date('Y-m-d H:i:s') >= $date;
    }

    /**
     * Tax validation
     *
     * @param Leafiny_Object $tax
     *
     * @return string
     */
    public function validate(Leafiny_Object $tax): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$tax->getData('title')) {
            return 'The title cannot be empty';
        }
        if (!$tax->getData('type')) {
            return 'The type cannot be empty';
        }

        return '';
    }
}
