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
 * Class Payment_Model_Payment
 */
abstract class Payment_Model_Payment extends Core_Model implements Commerce_Interface_Payment
{
    /**
     * Process the payment
     *
     * @param Leafiny_Object $sale
     *
     * @return void
     */
    abstract protected function processPayment(Leafiny_Object $sale): void;

    /**
     * Process payment
     *
     * @param Leafiny_Object $sale
     * @throws Exception|Throwable
     */
    public function execute(Leafiny_Object $sale): void
    {
        if ($sale->getData('payment_method') !== $this->getMethod()) {
            return;
        }

        $this->processPayment($sale);
    }

    /**
     * Retrieve payment title
     *
     * @return string
     */
    public function getTitle(): string
    {
        $title = $this->getCustom('title');

        return $title ? App::translate($title) : '';
    }

    /**
     * Payment method is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return (bool)$this->getCustom('is_enabled');
    }
}
