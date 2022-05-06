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
 * Class AddToCart_Block_Popup
 */
class AddToCart_Block_Popup extends Core_Block
{
    /**
     * Retrieve added product
     *
     * @return Leafiny_Object|null
     */
    public function getProduct(): ?Leafiny_Object
    {
        return $this->getCustom('product');
    }

    /**
     * Retrieve added item
     *
     * @return Leafiny_Object|null
     */
    public function getCartItem(): ?Leafiny_Object
    {
        return $this->getCustom('item');
    }

    /**
     * Retrieve product main image
     *
     * @param int $productId
     *
     * @return Leafiny_Object|null
     */
    public function getMainImage(int $productId): ?Leafiny_Object
    {
        /** @var Gallery_Model_Image $image */
        $image = App::getObject('model', 'gallery_image');

        try {
            return $image->getMainImage($productId, 'catalog_product');
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }

    /**
     * Retrieve checkout URL
     *
     * @return string
     */
    public function getCheckoutPath(): string
    {
        /** @var Commerce_Helper_Checkout $helperCheckout */
        $helperCheckout = $this->getHelper('checkout');

        return $helperCheckout->getStepUrl();
    }
}
