<?php

declare(strict_types=1);

/**
 * Class Blog_Helper_Data
 */
class Blog_Helper_Data extends Core_Helper
{
    /**
     * @var string URL_PARAM_PAGE
     */
    public const URL_PARAM_PAGE = '-blog-page-';

    /**
     * Retrieve Category Post
     *
     * @param int $categoryId
     * @param int $page
     *
     * @return array
     * @throws Exception
     */
    public function getCategoryPosts(int $categoryId, int $page = 1): array
    {
        /** @var Blog_Model_Post $model */
        $model = App::getObject('model', 'blog_post');

        $filters = [
            [
                'column'   => 'status',
                'value'    => 1,
                'operator' => '=',
            ],
        ];

        $orders = [
            [
                'order' => 'publish_date',
                'dir'   => 'DESC',
            ],
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ]
        ];

        $limit = [$this->getOffset($page), $this->getLimit()];

        return $model->addCategoryFilter($categoryId)->getList($filters, $orders, $limit);
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
