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
 * Class Commerce_Observer_Sale_UpdateCoupon
 */
class Commerce_Observer_Sale_UpdateCoupon extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object $sale */
        $sale = $object->getData('sale');

        if (!$sale->getData('coupon_id')) {
            return;
        }

        try {
            /** @var Commerce_Model_Cart_Rule_Coupon $couponModel */
            $couponModel = App::getObject('model', 'cart_rule_coupon');

            $coupon = $couponModel->get($sale->getData('coupon_id'));
            if (!$coupon->getData('coupon_id')) {
                return;
            }

            $coupon->setData('used', (int)$coupon->getData('used') + 1);
            $couponModel->save($coupon);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
    }
}