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
 * Class CartRule_Block_Checkout_Coupon
 */
class CartRule_Block_Checkout_Coupon extends Commerce_Block_Checkout
{
    /**
     * Retrieve update coupon URL
     *
     * @return string
     */
    public function getCouponUrl(): string
    {
        return $this->getUrl('/checkout/coupon/update/');
    }
}
