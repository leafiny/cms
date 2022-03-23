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
 * Class Commerce_Observer_Checkout_Shipping_Save
 */
class Commerce_Observer_Checkout_Shipping_Save extends Commerce_Observer_Checkout_Process
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

        if (!$post->getData('shipping_method')) {
            $this->error('The shipping method is required');
            return;
        }

        try {
            /** @var Commerce_Helper_Cart $cartHelper */
            $cartHelper = App::getSingleton('helper', 'cart');

            /** @var Commerce_Helper_Shipping $shippingHelper */
            $shippingHelper = App::getSingleton('helper', 'shipping');
            $shippingHelper->assignShippingMethod(
                $cartHelper->getCurrentId(),
                $post->getData('shipping_method')
            );

            $cartHelper->calculation($cartHelper->getCurrentId());
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
            $this->error('An error occurred with the cart. Please contact us.');
        }
    }
}
