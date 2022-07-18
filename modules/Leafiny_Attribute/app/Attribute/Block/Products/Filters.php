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
     * Retrieve total of filtered items in the category
     *
     * @param int|null $categoryId
     *
     * @return int|null
     * @throws Exception
     */
    public function getTotalItems(?int $categoryId = null): ?int
    {
        /** @var Catalog_Helper_Catalog_Product $helper */
        $helper = App::getSingleton('helper', 'catalog_product');

        return $helper->getTotalCategoryProducts($categoryId);
    }

    /**
     * Retrieve all item identifiers in the category
     *
     * @param int|null $categoryId
     *
     * @return int[]
     * @throws Exception
     */
    public function getItemIds(?int $categoryId = null): array
    {
        if ($this->getData('_item_ids')) {
            return $this->getData('_item_ids');
        }

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

        $this->setData('_item_ids', $productIds);

        return $this->getData('_item_ids');
    }
}
