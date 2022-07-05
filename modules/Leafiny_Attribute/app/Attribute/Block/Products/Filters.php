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
class Attribute_Block_Products_Filters extends Core_Block
{
    /**
     * Retrieve current filters
     *
     * @param Core_Page $page
     *
     * @return Leafiny_Object
     */
    public function getCurrentFilters(Core_Page $page): Leafiny_Object
    {
        return $page->getParams(['get']);
    }

    /**
     * Retrieve current page URL
     *
     * @param Core_Page $page
     *
     * @return string
     */
    public function getPageUrl(Core_Page $page): string
    {
        return App::getUrlRewrite($page->getObjectKey(), 'category') . '#catalog';
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
     * Retrieve filters
     *
     * @param int|null $categoryId
     *
     * @return array
     */
    public function getFilters(?int $categoryId = null): array
    {
        /** @var Attribute_Model_Attribute $attributeModel */
        $attributeModel = App::getSingleton('model', 'attribute');

        try {
            return $attributeModel->getFilterableEntityAttributes(
                $this->getCategoryProductIds($categoryId),
                'catalog_product',
                App::getLanguage()
            );
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }

    /**
     * Retrieve all category product ids
     *
     * @param int|null $categoryId
     *
     * @return int[]
     * @throws Exception
     */
    public function getCategoryProductIds(?int $categoryId = null): array
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
