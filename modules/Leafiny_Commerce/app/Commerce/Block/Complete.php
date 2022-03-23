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
 * Class Commerce_Block_Checkout_Complete
 */
class Commerce_Block_Complete extends Core_Block
{
    /**
     * Retrieve sale
     *
     * @return Leafiny_Object
     */
    public function getSale(): Leafiny_Object
    {
        $sale = new Leafiny_Object();

        try {
            if ($this->getSession()) {
                $saleId = $this->getSession()->get('tmp_last_sale_id');

                /** @var Commerce_Model_Sale $model */
                $model = App::getSingleton('model', 'sale');
                $sale = $model->get((int)$saleId);
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $sale;
    }

    /**
     * Retrieve payment static block
     *
     * @return string
     */
    public function getPaymentBlock(): string
    {
        return $this->getSale()->getData('payment_method') . '.payment.complete';
    }

    /**
     * Retrieve session
     *
     * @return Leafiny_Session|null
     */
    public function getSession(): ?Leafiny_Session
    {
        return App::getSession(Core_Template_Abstract::CONTEXT_DEFAULT);
    }
}
