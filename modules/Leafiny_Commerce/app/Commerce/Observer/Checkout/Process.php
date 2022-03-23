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
 * Class Commerce_Observer_Checkout_Process
 */
abstract class Commerce_Observer_Checkout_Process extends Core_Observer implements Core_Interface_Observer
{
    /**
     * The current sale
     *
     * @var Leafiny_Object|null
     */
    protected $currentSale = null;

    /**
     * The checkout page
     *
     * @var Core_Page|null  $checkout
     */
    protected $checkout = null;

    /**
     * The step code
     *
     * @var Leafiny_Object|null
     */
    protected $step = null;

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
        /** @var Commerce_Page_Checkout $checkout */
        $checkout = $object->getData('checkout');
        /** @var Leafiny_Object $step */
        $step = $object->getData('step');

        $this->checkout = $checkout;
        $this->step = $step;
    }

    /**
     * Retrieve the checkout page
     *
     * @return Core_Page|null
     */
    public function getCheckout(): ?Core_Page
    {
        return $this->checkout;
    }

    /**
     * Retrieve the step
     *
     * @return Leafiny_Object
     */
    public function getStep(): Leafiny_Object
    {
        return $this->step ?: new Leafiny_Object();
    }

    /**
     * Validate sale
     *
     * @return void
     */
    protected function validateSale(): void
    {
        if (!$this->getCurrentSale()->getData('sale_id')) {
            $this->error('The cart is empty');
        }
    }

    /**
     * Validate email address
     *
     * @return void
     * @throws Exception
     */
    protected function validateEmail(): void
    {
        if (!$this->getCurrentSale()->getData('email')) {
            $this->error('The email address is required');
        }
    }

    /**
     * Validate addresses
     *
     * @return void
     */
    protected function validateAddresses(): void
    {
        /** @var Commerce_Helper_Cart $saleHelper */
        $saleHelper = App::getSingleton('helper', 'cart');
        /** @var Commerce_Helper_Shipping $shippingHelper */
        $shippingHelper = App::getSingleton('helper', 'shipping');

        $shipping = $saleHelper->getAddress('shipping');
        if (!$shipping) {
            $this->error('Shipping address is required');
            return;
        }
        $errors = $saleHelper->validateAddress($shipping);
        foreach ($errors as $error) {
            $this->error($error);
            return;
        }
        $countryCode = $shipping->getData('country_code');
        if (!in_array($countryCode, $shippingHelper->getAllowedCountries())) {
            $this->error('The shipping country is not allowed');
            return;
        }

        $billing = $saleHelper->getAddress('billing');
        if (!$billing) {
            $this->error('Billing address is required');
            return;
        }
        $errors = $saleHelper->validateAddress($billing);
        foreach ($errors as $error) {
            $this->error($error);
            return;
        }
    }

    /**
     * Validate shipping
     *
     * @return void
     * @throws Exception
     */
    protected function validateShipping(): void
    {
        /** @var Commerce_Helper_Cart $saleHelper */
        $saleHelper = App::getSingleton('helper', 'cart');

        $sale = $this->getCurrentSale();
        $shippingMethod = $sale->getData('shipping_method');
        if (!$shippingMethod) {
            $this->error('The shipping method is required');
        }

        /** @var Commerce_Helper_Shipping $shippingHelper */
        $shippingHelper = App::getSingleton('helper', 'shipping');
        $shippingAddress = $saleHelper->getAddress('shipping');
        if ($shippingAddress) {
            if (!$shippingHelper->isMethodValidForAddress($shippingMethod, $shippingAddress)) {
                $this->error('The shipping method is not available');
            }
        }
    }

    /**
     * Validate payment
     *
     * @return void
     * @throws Exception
     */
    protected function validatePayment(): void
    {
        $sale = $this->getCurrentSale();
        if (!$sale->getData('payment_method')) {
            $this->error('The payment method is required');
        }

        /** @var Commerce_Helper_Payment $helper */
        $helper = App::getSingleton('helper', 'payment');
        if (!$helper->isMethodValid($sale->getData('payment_method'))) {
            $this->error('The payment method is not available');
        }
    }

    /**
     * Validate payment
     *
     * @return void
     * @throws Exception
     */
    protected function validateAgreements(): void
    {
        $sale = $this->getCurrentSale();
        if (!$sale->getData('agreements')) {
            $this->error('Please accept the general conditions of sale');
        }
    }

    /**
     * Retrieve current sale
     *
     * @return Leafiny_Object|null
     */
    public function getCurrentSale(): Leafiny_Object
    {
        if ($this->currentSale !== null) {
            return $this->currentSale;
        }

        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');
        $this->currentSale = new Leafiny_Object();

        try {
            $current = $helper->getCurrentSale();
            if ($current) {
                $this->currentSale = $helper->getCurrentSale();
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $this->currentSale;
    }

    /**
     * Send error
     *
     * @param string $message
     *
     * @return void
     */
    protected function error(string $message): void
    {
        /** @var Commerce_Helper_Checkout $helper */
        $helper = App::getObject('helper', 'checkout');

        /** @var Commerce_Page_Checkout $page */
        $page = $this->getCheckout();

        $page->setErrorMessage(App::translate($message));

        if ($page->isAjax()) {
            $page->responseJson($this->getStep()->getData('code'));
        }

        $page->redirect($page->getUrl($helper->getStepUrl($this->getStep()->getData('code'))));
    }
}
