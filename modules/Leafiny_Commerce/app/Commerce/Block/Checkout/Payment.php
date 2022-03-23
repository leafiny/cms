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
 * Class Commerce_Block_Checkout_Payment
 */
class Commerce_Block_Checkout_Payment extends Commerce_Block_Checkout
{

    /**
     * Retrieve payment methods
     *
     * @return Commerce_Interface_Payment[]
     */
    public function getPaymentMethods(): array
    {
        /** @var Commerce_Helper_Payment $helper */
        $helper = App::getSingleton('helper', 'payment');

        return $helper->getPaymentMethods();
    }

    /**
     * Retrieve current payment method
     *
     * @return string|null
     * @throws Exception
     */
    public function getCurrentMethod(): ?string
    {
        return $this->getSale()->getData('payment_method');
    }
}
