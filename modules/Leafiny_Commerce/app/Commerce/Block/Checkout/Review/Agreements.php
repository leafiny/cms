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
 * Class Commerce_Block_Checkout_Review_Agreements
 */
class Commerce_Block_Checkout_Review_Agreements extends Commerce_Block_Checkout
{
    /**
     * Retrieve agreements URL
     *
     * @return string
     */
    public function getAgreementsUrl(): string
    {
        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');

        return $this->getUrl($helper->getCustom('agreements_url'));
    }
}
