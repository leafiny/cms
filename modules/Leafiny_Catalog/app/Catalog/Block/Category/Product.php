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
 * Class Catalog_Block_Category_Product
 */
class Catalog_Block_Category_Product extends Core_Block
{
    /**
     * Retrieve products
     *
     * @param int $categoryId
     *
     * @return array
     * @throws Exception
     */
    public function getProducts(int $categoryId): array
    {
        /** @var Catalog_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'catalog_product');

        return $helper->getCategoryProducts($categoryId, $this->getPageNumber());
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
     * Retrieve current page number
     *
     * @return int
     */
    public function getPageNumber(): int
    {
        return (int)$this->getParentObjectParams()->getData(Catalog_Helper_Data::URL_PARAM_PAGE) ?: 1;
    }
}
