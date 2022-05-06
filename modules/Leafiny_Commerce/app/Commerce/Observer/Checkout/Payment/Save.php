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
 * Class Commerce_Observer_Checkout_Payment_Save
 */
class Commerce_Observer_Checkout_Payment_Save extends Commerce_Observer_Checkout_Process
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $object): void
    {
        parent::execute($object);

        $post = $this->getCheckout()->getPost();

        if (!$post->hasData()) {
            return;
        }

        if (!$this->getCurrentSale()->getData('sale_id')) {
            return;
        }

        if (!$post->getData('payment_method')) {
            $this->error('The payment method is required');
            return;
        }

        try {
            $sale = $this->getCurrentSale();

            $sale->setData('payment_method', $post->getData('payment_method'));
            $sale->setData(
                'payment_data',
                json_encode(
                    [
                        'redirect' => $this->getOrderCompleteUrl()
                    ]
                )
            );

            /** @var Commerce_Model_Sale $saleModel */
            $saleModel = App::getSingleton('model', 'sale');
            $saleModel->save($sale);

            /** @var Commerce_Helper_Cart $cartHelper */
            $cartHelper = App::getSingleton('helper', 'cart');
            $cartHelper->calculation($cartHelper->getCurrentId());
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->error('An error occurred with the cart. Please contact us.');
        }
    }

    /**
     * Retrieve order complete URL
     *
     * @return string
     */
    public function getOrderCompleteUrl(): string
    {
        /** @var Commerce_Helper_Order $helperOrder */
        $helperOrder = App::getSingleton('helper', 'order');

        return $this->getCheckout()->getUrl(
            $helperOrder->getCustom('order_complete_url') ?: '/checkout/order/complete/'
        );
    }
}
