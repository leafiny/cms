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
 * Class Attribute_Block_Products_Filters
 */
class Attribute_Block_Products_Filters extends Attribute_Block_Filters
{
    /**
     * Retrieve entity type name
     *
     * @return string
     */
    public function getEntityType(): string
    {
        return 'catalog_product';
    }

    /**
     * Are there any products?
     *
     * @param int $categoryId
     *
     * @return bool
     * @throws Exception
     */
    public function hasItems(int $categoryId): bool
    {
        /** @var Catalog_Helper_Catalog_Product $helper */
        $helper = App::getSingleton('helper', 'catalog_product');

        return (bool)$helper->getTotalCategoryProducts($categoryId);
    }

    /**
     * Retrieve all category product ids
     *
     * @param int|null $categoryId
     *
     * @return int[]
     * @throws Exception
     */
    public function getItemIds(?int $categoryId = null): array
    {
        /** @var Catalog_Helper_Catalog_Product $helper */
        $helper = App::getSingleton('helper', 'catalog_product');
        /** @var Catalog_Model_Product $model */
        $model = App::getSingleton('model', 'catalog_product');

        if ($categoryId) {
            $model->addCategoryFilter($categoryId);
        }

        $products = $model->getList($helper->getFilters(), [], null, [], ['main_table.product_id']);

        $productIds = [];
        foreach ($products as $product) {
            $productIds[] = (int)$product->getData('product_id');
        }

        return $productIds;
    }
}
