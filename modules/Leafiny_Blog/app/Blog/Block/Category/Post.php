<?php

declare(strict_types=1);

/**
 * Class Blog_Block_Category_Post
 */
class Blog_Block_Category_Post extends Core_Block
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
        /** @var Blog_Helper_Data $helper */
        $helper = App::getObject('helper', 'blog_post');

        $page = $this->getParentObjectParams()->getData('p') ?: 1;

        return $helper->getCategoryPosts($categoryId, (int)$page);
    }
}
