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
 * Class Blog_Block_Post_Default
 */
class Blog_Block_Post_Default extends Core_Block
{
    /**
     * Retrieve post
     *
     * @return Leafiny_Object
     */
    public function getPost(): Leafiny_Object
    {
        return $this->getCustom('post');
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
