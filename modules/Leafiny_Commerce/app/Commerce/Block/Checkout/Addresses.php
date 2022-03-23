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
 * Class Commerce_Block_Checkout_Addresses
 */
class Commerce_Block_Checkout_Addresses extends Commerce_Block_Checkout
{
    /**
     * Retrieve allowed countries
     *
     * @return string[]
     */
    public function getCountries(): array
    {
        /** @var Core_Helper_Country $country */
        $country = App::getObject('helper_country');
        /** @var Commerce_Helper_Shipping $helper */
        $helper = App::getSingleton('helper', 'shipping');

        return array_intersect_key($country->getList(), array_flip($helper->getAllowedCountries()));
    }

    /**
     * Retrieve post data
     *
     * @param Core_Page $page
     *
     * @return Leafiny_Object
     */
    public function getPostData(Core_Page $page): Leafiny_Object
    {
        return $page->getTmpSessionData('checkout_addresses_post') ?: new Leafiny_Object();
    }
}
