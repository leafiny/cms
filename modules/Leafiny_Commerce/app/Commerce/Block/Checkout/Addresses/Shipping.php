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
 * Class Commerce_Block_Checkout_Addresses_Shipping
 */
class Commerce_Block_Checkout_Addresses_Shipping extends Commerce_Block_Checkout_Addresses
{
    /**
     * Retrieve Shipping Address
     *
     * @param Core_Page $page
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getShippingAddress(Core_Page $page): Leafiny_Object
    {
        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');

        $post = $this->getPostData($page);
        if ($post->getData('shipping')) {
            return $post->getData('shipping');
        }

        $address = $helper->getCurrentId() ? $helper->getAddress('shipping') : null;

        return $address ?: new Leafiny_Object();
    }

    /**
     * Retrieve email address
     *
     * @param Core_Page $page
     *
     * @return string
     */
    public function getEmail(Core_Page $page): string
    {
        $post = $this->getPostData($page);
        if ($post->getData('email')) {
            return (string)$post->getData('email');
        }

        return (string)$this->getSale()->getData('email');
    }
}
