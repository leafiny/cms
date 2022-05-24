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
 * Class Blog_Helper_Data
 */
class Blog_Helper_Data extends Core_Helper
{
    /**
     * @var string URL_PARAM_PAGE
     */
    public const URL_PARAM_PAGE = 'bp';
    /**
     * @var string COMMENT_FORM_DATA_KEY
     */
    public const COMMENT_FORM_DATA_KEY = 'form_comment_post_data';
    /**
     * @var string COMMENT_FORM_ERROR_KEY
     */
    public const COMMENT_FORM_ERROR_KEY = 'comment_form_error_message';
    /**
     * @var string COMMENT_FORM_SUCCESS_KEY
     */
    public const COMMENT_FORM_SUCCESS_KEY = 'comment_form_success_message';

    /**
     * @var null|Leafiny_Object[]
     */
    protected $categoryPosts = null;

    /**
     * Retrieve Category Post
     *
     * @param int $categoryId
     * @param int $page
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getCategoryPosts(int $categoryId, int $page = 1): array
    {
        if ($this->categoryPosts !== null) {
            return $this->categoryPosts;
        }

        /** @var Blog_Model_Post $model */
        $model = App::getObject('model', 'blog_post');

        $limit = [$this->getOffset($page), $this->getLimit()];

        $this->categoryPosts = $model->addCategoryFilter($categoryId)
            ->getList($this->getFilters(), $this->getOrders(), $limit);

        return $this->categoryPosts;
    }

    /**
     * Retrieve Category Post
     *
     * @param int $categoryId
     *
     * @return int
     * @throws Exception
     */
    public function getTotalCategoryPosts(int $categoryId): int
    {
        /** @var Blog_Model_Post $model */
        $model = App::getObject('model', 'blog_post');

        return count($model->addCategoryFilter($categoryId)->getList($this->getFilters()));
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
                'order' => 'publish_date',
                'dir'   => 'DESC',
            ],
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ]
        ];

        return $this->getCustom('sort_order') ?: $sortOrder;
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

        return array_merge($filters, $this->getCustom('filters') ?: []);
    }

    /**
     * Retrieve limit
     *
     * @return int
     */
    public function getLimit(): int
    {
        return $this->getCustom('post_per_page') ?: 10;
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
