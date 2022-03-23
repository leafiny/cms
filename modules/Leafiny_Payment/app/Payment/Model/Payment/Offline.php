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
 * Class Payment_Model_Payment_Offline
 */
abstract class Payment_Model_Payment_Offline extends Payment_Model_Payment
{
    /**
     * Process payment
     *
     * @param Leafiny_Object $sale
     * @throws Exception|Throwable
     */
    protected function processPayment(Leafiny_Object $sale): void
    {
        /** @var Commerce_Helper_Order $helper */
        $helper = App::getSingleton('helper', 'order');
        /** @var Commerce_Model_Sale $saleModel */
        $saleModel = App::getSingleton('model', 'sale');

        $sale->setData('payment_title', App::translate($this->getTitle()));
        $sale->setData('payment_state', 'pending');
        $sale->setData('payment_ref', uniqid());
        $sale->setData('no_history', true);
        $sale->setData('no_invoice', true);

        $saleModel->save($sale);

        $helper->complete($sale);
    }
}
