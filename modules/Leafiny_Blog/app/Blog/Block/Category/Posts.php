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
 * Class Blog_Block_Category_Posts
 */
class Blog_Block_Category_Posts extends Core_Block
{
    /**
     * Retrieve posts
     *
     * @param int $categoryId
     *
     * @return array
     * @throws Exception
     */
    public function getPosts(int $categoryId): array
    {
        if ($this->getCustom('posts')) {
            return $this->getCustom('posts');
        }

        /** @var Blog_Helper_Blog_Post $helper */
        $helper = App::getSingleton('helper', 'blog_post');

        return $helper->getCategoryPosts($categoryId, $this->getPageNumber());
    }

    /**
     * Retrieve current page number
     *
     * @return int
     */
    public function getPageNumber(): int
    {
        return (int)$this->getParentObjectParams()->getData(Blog_Helper_Blog_Post::URL_PARAM_PAGE) ?: 1;
    }
}
