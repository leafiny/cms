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
 * Class Commerce_Observer_Sale_Payment
 */
class Commerce_Observer_Sale_Payment extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Execute payment process after sale is placed
     *
     * @param Leafiny_Object $object
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object $sale */
        $sale = $object->getData('sale');

        if (!$sale->getData('payment_method')) {
            return;
        }

        $payment = App::getObject('model', $sale->getData('payment_method'));
        $payment->execute($sale);
    }
}
