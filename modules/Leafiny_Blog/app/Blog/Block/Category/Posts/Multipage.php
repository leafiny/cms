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
 * Class Blog_Block_Category_Posts_Multipage
 */
class Blog_Block_Category_Posts_Multipage extends Core_Block
{
    /**
     * @var int|null $totalPages
     */
    protected $totalPages = null;

    /**
     * Retrieve current page number
     *
     * @return int
     */
    public function getPageNumber(): int
    {
        return (int)$this->getParentObjectParams()->getData(Blog_Helper_Blog_Post::URL_PARAM_PAGE) ?: 1;
    }

    /**
     * Retrieve total pages number
     *
     * @return int
     * @throws Exception
     */
    public function getTotalPages(): int
    {
        if ($this->totalPages !== null) {
            return $this->totalPages;
        }

        $categoryId = $this->getCustom('category_id');

        if (!$categoryId) {
            return 0;
        }

        /** @var Blog_Helper_Blog_Post $helper */
        $helper = App::getSingleton('helper', 'blog_post');

        $this->totalPages = (int)ceil($helper->getTotalCategoryPosts($categoryId) / $helper->getLimit()) ?: 1;

        return $this->totalPages;
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
        $url = App::getUrlRewrite($page->getObjectKey(), 'category');

        if ($number > 1) {
            $url .= '?' . Blog_Helper_Blog_Post::URL_PARAM_PAGE . '=' . $number;
        }

        return $url;
    }
}
