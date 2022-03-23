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
 * Class Commerce_Helper_Payment
 */
class Commerce_Helper_Payment extends Core_Helper
{
    /**
     * Check method is valid
     *
     * @param string $methodName
     *
     * @return bool
     * @throws Exception
     */
    public function isMethodValid(string $methodName): bool
    {
        $methods = $this->getPaymentMethods();

        foreach ($methods as $method) {
            if ($method->getMethod() === $methodName) {
                return true;
            }
        }

        return false;
    }

    /**
     * Retrieve payment methods
     *
     * @return Commerce_Interface_Payment[]
     */
    public function getPaymentMethods(): array
    {
        $methods = $this->getCustom('payment_methods');

        if (!$methods) {
            return [];
        }

        $methods = array_filter($methods, 'strlen');
        asort($methods);
        $methods = array_keys($methods);

        $payments = [];

        foreach ($methods as $method) {
            $payment = $this->getPaymentMethod($method);

            if (!$payment->isEnabled()) {
                continue;
            }

            $payments[] = $payment;
        }

        App::dispatchEvent('payment_methods_load_after', ['subject' => $this, 'payments' => $payments]);

        return $payments;
    }

    /**
     * Retrieve payment method
     *
     * @param string $method
     *
     * @return Commerce_Interface_Payment
     */
    public function getPaymentMethod(string $method): Commerce_Interface_Payment
    {
        return App::getObject('model', $method);
    }
}
