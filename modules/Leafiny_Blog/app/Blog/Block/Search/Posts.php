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
 * Class Blog_Block_Search_Posts
 */
class Blog_Block_Search_Posts extends Core_Block
{
    /**
     * Retrieve posts
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getPosts(): array
    {
        if (!$this->getPostIds()) {
            return [];
        }

        $filters = [
            [
                'column' => 'status',
                'value'  => 1,
            ],
            [
                'column' => 'language',
                'value'  => App::getLanguage(),
            ],
            [
                'column'   => 'post_id',
                'value'    => $this->getPostIds(),
                'operator' => 'IN',
            ],
        ];

        $orders = [
            [
                'order'  => 'post_id',
                'dir'    => 'ASC',
                'custom' => $this->getPostIds(),
            ]
        ];

        /** @var Blog_Model_Post $model */
        $model = App::getObject('model', 'blog_post');

        return $model->getList($filters, $orders, $this->getLimit());
    }

    /**
     * Retrieve limit
     *
     * @return int[]
     */
    public function getLimit(): array
    {
        return $this->getCustom('limit') ?? [0, 100];
    }

    /**
     * Retrieve object Ids
     *
     * @return array
     */
    public function getPostIds(): array
    {
        return $this->getCustom('response') ?? [];
    }
}
