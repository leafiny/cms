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
 * Interface Commerce_Interface_Payment
 */
interface Commerce_Interface_Payment
{
    /**
     * Add Log
     *
     * @param Leafiny_Object $sale
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $sale): void;

    /**
     * Retrieve payment title
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Retrieve payment method
     *
     * @return string
     */
    public function getMethod(): string;

    /**
     * Payment method is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool;
}
