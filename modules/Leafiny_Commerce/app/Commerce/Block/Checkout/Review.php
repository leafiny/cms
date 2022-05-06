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
 * Class Commerce_Block_Checkout_Review
 */
class Commerce_Block_Checkout_Review extends Commerce_Block_Checkout
{
    /**
     * Retrieve place order URL
     *
     * @return string
     */
    public function getPlaceOrderUrl(): string
    {
        return $this->getUrl('/checkout/order/place/');
    }
}
