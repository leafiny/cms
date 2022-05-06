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
 * Class Commerce_Block_Checkout_Addresses_Billing
 */
class Commerce_Block_Checkout_Addresses_Billing extends Commerce_Block_Checkout_Addresses
{
    /**
     * Retrieve Billing Address
     *
     * @param Core_Page $page
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getBillingAddress(Core_Page $page): Leafiny_Object
    {
        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');

        $post = $this->getPostData($page);
        if ($post->getData('billing')) {
            return $post->getData('billing');
        }

        $address = $helper->getCurrentId() ? $helper->getAddress('billing', $this->getSale()) : null;

        return $address ?: new Leafiny_Object();
    }
}
