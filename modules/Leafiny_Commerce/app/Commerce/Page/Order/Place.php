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
 * Class Commerce_Page_Checkout_Order_Place
 */
class Commerce_Page_Order_Place extends Commerce_Page_Checkout
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        $this->setData('checkout_action_no_process', true);
        parent::action();

        App::dispatchEvent(
            'checkout_action_save_' . $this->getStepCode(),
            ['checkout' => $this, 'step' => $this->getStep()]
        );
        App::dispatchEvent(
            'checkout_action_validate_' . $this->getStepCode(),
            ['checkout' => $this, 'step' => $this->getStep()]
        );

        try {
            /** @var Commerce_Helper_Cart $helperCart */
            $helperCart = App::getSingleton('helper', 'cart');

            $status = $this->getDefaultStatusCode();

            $sale = $helperCart->getCurrentSale();
            $sale->setData('created_at', date('Y-m-d H:i:s'));
            $sale->setData('status', $status);

            /** @var Commerce_Model_Sale $saleModel */
            $saleModel = App::getSingleton('model', 'sale');

            App::dispatchEvent('commerce_order_place', ['sale' => $sale]);

            $saleId = $saleModel->save($sale);
            $helperCart->calculation($saleId);

            /** @var Commerce_Model_Sale_History $historyModel */
            $historyModel = App::getSingleton('model', 'sale_history');
            $historyModel->save(
                new Leafiny_Object(
                    [
                        'sale_id'     => $saleId,
                        'status_code' => $status,
                        'language'    => App::getLanguage()
                    ]
                )
            );

            $this->setTmpSessionData('last_sale_id', $saleId);

            $paymentData = json_decode($sale->getData('payment_data'), true);
            if (isset($paymentData['redirect'])) {
                $this->redirect($paymentData['redirect']);
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->setErrorMessage(
                $this->translate('An error occurred with the order. Please contact us.')
            );
        }

        $this->redirect($this->getRefererUrl());
    }

    /**
     * Retrieve status
     *
     * @return string
     */
    public function getDefaultStatusCode(): string
    {
        /** @var Commerce_Model_Sale $saleModel */
        $saleModel = App::getSingleton('model', 'sale');

        return $saleModel->getCustom('default_status_code') ?: Commerce_Model_Sale_Status::SALE_STATUS_PENDING_PAYMENT;
    }

    /**
     * Retrieve the final step.
     *
     * @return Leafiny_Object
     */
    public function getStep(): Leafiny_Object
    {
        $steps = $this->getSteps();

        return end($steps);
    }

    /**
     * Retrieve the final step code. Allow to validate all steps.
     *
     * @return string
     */
    public function getStepCode(): string
    {
        return (string)$this->getStep()->getData('code');
    }
}
