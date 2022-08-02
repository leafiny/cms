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
 * Class Commerce_Block_Checkout_Cart_Items
 */
class Commerce_Block_Checkout_Cart_Items extends Commerce_Block_Checkout
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
     * Retrieve update items URL
     *
     * @return string
     */
    public function getUpdateItemsUrl(): string
    {
        return $this->getUrl('/checkout/items/update/');
    }

    /**
     * Retrieve product main image
     *
     * @param int $productId
     *
     * @return Leafiny_Object|null
     */
    public function getProductImage(int $productId): ?Leafiny_Object
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
     * Render options if needed
     *
     * @param string|null $options
     *
     * @return void
     * @throws Exception
     */
    public function renderOption(?string $options): void
    {
        if ($options) {
            /** @var array $result */
            $result = json_decode($options, true);

            if (!empty($result['render_block'])) {
                $this->blockHtml((string)$result['render_block'], $result);
            }
        }
    }

    /**
     * Retrieve max item quantity
     *
     * @param Leafiny_Object $item
     *
     * @return int
     */
    public function getMaxQty(Leafiny_Object $item): int
    {
        $maxQty = $item->getData('max_qty');
        if ($maxQty > 10) {
            $maxQty = 10;
        }
        if ($maxQty <= 0) {
            $maxQty = 1;
        }

        return $maxQty;
    }
}
