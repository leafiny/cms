<?php

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
            ->getList($this->getFilters(), $this->getOrders(), $limit);

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

        return count($model->addCategoryFilter($categoryId)->getList($this->getFilters()));
    }

    /**
     * Retrieve product orders
     *
     * @return string[][]
     */
    public function getOrders(): array
    {
        return [
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ]
        ];
    }

    /**
     * Retrieve product filters
     *
     * @return array[]
     */
    public function getFilters(): array
    {
        return [
            [
                'column'   => 'status',
                'value'    => 1,
                'operator' => '=',
            ],
        ];
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
