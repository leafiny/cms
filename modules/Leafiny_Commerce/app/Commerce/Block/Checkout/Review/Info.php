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
 * Class Commerce_Block_Checkout_Review_Info
 */
class Commerce_Block_Checkout_Review_Info extends Commerce_Block_Checkout
{
    /**
     * Retrieve payment method
     *
     * @return Commerce_Interface_Payment|null
     */
    public function getPaymentMethod(): ?Commerce_Interface_Payment
    {
        $method = $this->getSale()->getData('payment_method');

        if ($method) {
            /** @var Commerce_Interface_Payment $payment */
            return App::getObject('model', $method);
        }

        return null;
    }

    /**
     * Retrieve shipping method
     *
     * @return Leafiny_Object|null
     */
    public function getShippingMethod(): ?Leafiny_Object
    {
        $method = $this->getSale()->getData('shipping_method');

        if ($method) {
            try {
                /** @var Commerce_Model_Shipping $shippingModel */
                $shippingModel = App::getObject('model', 'shipping');
                $method = $shippingModel->get($method, 'method');
                if ($method->getData('shipping_id')) {
                    return $method;
                }
            } catch (Throwable $throwable) {
                App::log($throwable, Core_Interface_Log::ERR);
            }
        }

        return null;
    }
}
