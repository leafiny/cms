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
 * Class Commerce_Observer_Checkout_Addresses_Save
 */
class Commerce_Observer_Checkout_Addresses_Save extends Commerce_Observer_Checkout_Process
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

        $this->getCheckout()->setTmpSessionData('checkout_addresses_post', $post);

        if (!$post->getData('email')) {
            $this->error('The email address is required');
            return;
        }

        $shippingAddress = $post->getData('shipping');
        if (!$shippingAddress) {
            $this->error('The shipping address is required');
            return;
        }

        $billingAddress  = $post->getData('billing');
        if (!$billingAddress) {
            $this->error('The billing address is required');
            return;
        }

        if ($billingAddress->getData('same_as_shipping')) {
            $billingAddress->setData('same_as_shipping', 1);
            $billingAddress->addData($shippingAddress->getData());
        } else {
            $billingAddress->setData('same_as_shipping', 0);
        }

        /** @var Commerce_Helper_Cart $cartHelper */
        $cartHelper = App::getSingleton('helper', 'cart');

        $errors = $cartHelper->validateAddress($shippingAddress);
        foreach ($errors as $error) {
            $this->error($error);
            return;
        }

        $errors = $cartHelper->validateAddress($billingAddress);
        foreach ($errors as $error) {
            $this->error($error);
            return;
        }

        try {
            $sale = $this->getCurrentSale();

            /** @var Commerce_Model_Sale_Address $addressModel */
            $addressModel = App::getSingleton('model', 'sale_address');

            $shipping = $cartHelper->getAddress('shipping', $sale);
            if (!$shipping) {
                $shipping = new Leafiny_Object(
                    [
                        'sale_id' => $sale->getData('sale_id'),
                        'type'    => 'shipping',
                    ]
                );
            }
            $shipping->addData($shippingAddress->getData());
            $addressModel->save($shipping);

            $billing = $cartHelper->getAddress('billing', $sale);
            if (!$billing) {
                $billing = new Leafiny_Object(
                    [
                        'sale_id' => $sale->getData('sale_id'),
                        'type'    => 'billing',
                    ]
                );
            }
            $billing->addData($billingAddress->getData());
            $addressModel->save($billing);

            /** @var Commerce_Model_Sale $saleModel */
            $saleModel = App::getSingleton('model', 'sale');

            $sale->setData('email', $post->getData('email'));
            $sale->setData('customer', $billing->getData('firstname') . ' ' . $billing->getData('lastname'));

            $saleId = $saleModel->save($sale);

            $cartHelper->calculation($saleId);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->error('An error occurred with the cart. Please contact us.');
        }
    }
}
