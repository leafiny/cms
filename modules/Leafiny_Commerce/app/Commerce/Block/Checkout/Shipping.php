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
 * Class Commerce_Block_Checkout_Shipping
 */
class Commerce_Block_Checkout_Shipping extends Commerce_Block_Checkout
{
    /**
     * Retrieve current shipping address
     *
     * @var null|Leafiny_Object
     */
    protected $shippingAddress = null;

    /**
     * Retrieve payment methods
     *
     * @return Commerce_Interface_Payment[]
     * @throws Exception
     */
    public function getShippingMethods(): array
    {
        /** @var Commerce_Helper_Shipping $helper */
        $helper = App::getSingleton('helper', 'shipping');

        return $helper->getMethodsByAddress($this->getShippingAddress());
    }

    /**
     * Retrieve method prices
     *
     * @param string $method
     *
     * @return Leafiny_Object
     */
    public function getMethodPrices(string $method): Leafiny_Object
    {
        /** @var Commerce_Helper_Shipping $shippingHelper */
        $shippingHelper = App::getSingleton('helper', 'shipping');

        return $shippingHelper->getMethodPrices((int)$this->getSale()->getData('sale_id'), $method);
    }

    /**
     * Retrieve current shipping method
     *
     * @return string|null
     * @throws Exception
     */
    public function getCurrentMethod(): ?string
    {
        return $this->getSale()->getData('shipping_method');
    }

    /**
     * Retrieve Shipping Address
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getShippingAddress(): Leafiny_Object
    {
        if ($this->shippingAddress !== null) {
            return $this->shippingAddress;
        }

        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');
        $address = $helper->getAddress('shipping');

        $this->shippingAddress = $address ?: new Leafiny_Object();

        return $this->shippingAddress;
    }
}
