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
 * Class Commerce_Observer_Product_List_FinalPrice
 */
class Commerce_Observer_Product_List_FinalPrice extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object[] $products */
        $products = $object->getData('result');
        /** @var Commerce_Helper_Tax $taxHelper */
        $taxHelper = App::getSingleton('helper', 'tax');
        /** @var Commerce_Helper_Cart $saleHelper */
        $saleHelper = App::getSingleton('helper', 'cart');

        foreach ($products as $product) {
            if ($product->getData('price') === null) {
                $product->setData('price', 0);
            }

            $taxHelper->calculatePrices($product, $saleHelper->getAddress('shipping'));
        }
    }
}
