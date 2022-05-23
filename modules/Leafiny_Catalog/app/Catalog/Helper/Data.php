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
 * Class Catalog_Helper_Data
 */
class Catalog_Helper_Data extends Core_Helper
{
    /**
     * @var string URL_PARAM_PAGE
     */
    public const URL_PARAM_PAGE = 'cp';

    /**
     * @var null|Leafiny_Object[]
     */
    protected $categoryProducts = null;

    /**
     * Retrieve Category Products
     *
     * @param int $categoryId
     * @param int $page
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getCategoryProducts(int $categoryId, int $page = 1): array
    {
        if ($this->categoryProducts !== null) {
            return $this->categoryProducts;
        }

        /** @var Catalog_Model_Product $model */
        $model = App::getObject('model', 'catalog_product');

        $limit = [$this->getOffset($page), $this->getLimit()];

        $this->categoryProducts = $model->addCategoryFilter($categoryId)
            ->getList($this->getFilters(), $this->getOrders(), $limit, $this->getJoins());

        return $this->categoryProducts;
    }

    /**
     * Retrieve Category Products
     *
     * @param int $categoryId
     *
     * @return int
     * @throws Exception
     */
    public function getTotalCategoryProducts(int $categoryId): int
    {
        /** @var Catalog_Model_Product $model */
        $model = App::getObject('model', 'catalog_product');

        $list = $model->addCategoryFilter($categoryId)->getList($this->getFilters(), [], null, $this->getJoins());

        return count($list);
    }

    /**
     * Retrieve sort order
     *
     * @return string[][]
     */
    public function getOrders(): array
    {
        $sortOrder = [
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ],
        ];

        return $this->getCustom('product_sort_order') ?: $sortOrder;
    }

    /**
     * Retrieve filters
     *
     * @return array[]
     */
    public function getFilters(): array
    {
        $filters = [
            'status' => [
                'column' => 'status',
                'value'  => 1,
            ],
            'language' => [
                'column' => 'language',
                'value'  => App::getLanguage(),
            ],
        ];

        return array_merge($filters, $this->getCustom('product_filters') ?: []);
    }

    /**
     * Retrieve limit
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->getCustom('product_per_page') ?: 10;
    }

    /**
     * Retrieve join list
     *
     * @return array
     */
    public function getJoins(): array
    {
        return $this->getCustom('product_joins') ?: [];
    }

    /**
     * Retrieve limit offset
     *
     * @param int $page
     *
     * @return int
     */
    public function getOffset(int $page): int
    {
        return ($page - 1) * $this->getLimit();
    }
}
