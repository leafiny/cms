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
        return (int)$this->getParentObjectParams()->getData(Blog_Helper_Data::URL_PARAM_PAGE) ?: 1;
    }

    /**
     * Retrieve total pages number
     *
     * @param int $categoryId
     *
     * @return int
     * @throws Exception
     */
    public function getTotalPages(int $categoryId): int
    {
        /** @var Blog_Helper_Data $helper */
        $helper = App::getSingleton('helper', 'blog_post');

        return (int)ceil($helper->getTotalCategoryPosts($categoryId) / $helper->getLimit()) ?: 1;
    }

    /**
     * Retrieve page URL
     *
     * @param Core_Page $page
     * @param int       $number
     *
     * @return string
     */
    public function getPageUrl(Core_Page $page, int $number): string
    {
        $url = $page->getUrl($page->getObjectIdentifier());

        if ($number > 1) {
            $url .= '?' . Blog_Helper_Data::URL_PARAM_PAGE . '=' . $number;
        }

        return $url;
    }
}
