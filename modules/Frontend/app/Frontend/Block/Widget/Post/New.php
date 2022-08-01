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

class Frontend_Block_Widget_Post_New extends Core_Block
{
    /**
     * Retrieve last posts
     *
     * @return Leafiny_Object[]
     */
    Public function getPosts(): array
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

        $sortOrders = [
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ],
        ];

        $limit = [0, $this->getCustom('number') ?? 2];

        /** @var Blog_Model_Post $model */
        $model = App::getSingleton('model', 'blog_post');

        try {
            return $model->getList($filters, $sortOrders, $limit);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }
}
