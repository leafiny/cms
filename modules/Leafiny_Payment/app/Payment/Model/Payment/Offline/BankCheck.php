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
 * Class Payment_Model_Payment_Offline_BankCheck
 */
class Payment_Model_Payment_Offline_BankCheck extends Payment_Model_Payment_Offline
{
    /**
     * @var string PAYMENT_METHOD
     */
    const PAYMENT_METHOD = 'bank_check';

    /**
     * Retrieve method name
     *
     * @return string
     */
    public function getMethod(): string
    {
        return self::PAYMENT_METHOD;
    }
}
