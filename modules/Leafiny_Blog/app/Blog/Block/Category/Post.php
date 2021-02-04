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
     * Retrieve main image
     *
     * @param int $postId
     *
     * @return Leafiny_Object|null
     */
    public function getMainImage(int $postId): ?Leafiny_Object
    {
        /** @var Gallery_Model_Image $image */
        $image = App::getObject('model', 'gallery_image');

        try {
            return $image->getMainImage($postId, 'blog_post');
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }
}
