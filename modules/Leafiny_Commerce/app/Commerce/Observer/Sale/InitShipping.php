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
 * Class Commerce_Observer_Sale_InitShipping
 */
class Commerce_Observer_Sale_InitShipping extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Set default shipping method to the sale
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var int $saleId */
        $saleId = $object->getData('sale_id');
        /** @var Commerce_Helper_Shipping $shippingHelper */
        $shippingHelper = App::getSingleton('helper', 'shipping');

        $shippingHelper->assignShippingMethod($saleId);
    }
}
