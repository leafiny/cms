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
 * Class Commerce_Block_MiniCart
 */
class Commerce_Block_MiniCart extends Core_Block
{
    /**
     * Retrieve remove item URL
     *
     * @param Leafiny_Object $item
     *
     * @return string
     */
    public function getRemoveUrl(Leafiny_Object $item): string
    {
        return $this->getUrl('/checkout/item/remove/' . $this->crypt((string)$item->getData('item_id')) . '/');
    }

    /**
     * Retrieve checkout URL
     *
     * @return string
     */
    public function getCheckoutUrl(): string
    {
        return $this->getUrl('checkout.html');
    }

    /**
     * Retrieve cart items
     *
     * @return Leafiny_Object[]
     */
    public function getItems(): array
    {
        /** @var Commerce_Model_Sale_Item $model */
        $model = App::getObject('model', 'sale_item');
        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');

        try {
            if ($helper->getCurrentId()) {
                return $model->getItems($helper->getCurrentId());
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }

    /**
     * Retrieve current sale
     *
     * @return Leafiny_Object|null
     */
    public function getSale(): ?Leafiny_Object
    {
        /** @var Commerce_Helper_Cart $helper */
        $helper = App::getSingleton('helper', 'cart');

        try {
            return $helper->getCurrentSale();
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }
}