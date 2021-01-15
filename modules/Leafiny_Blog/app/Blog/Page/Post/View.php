<?php

declare(strict_types=1);

/**
 * Class Blog_Page_Post_View
 */
class Blog_Page_Post_View extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     * @throws Exception
     */
    public function action(): void
    {
        parent::action();

        if (!$this->getObjectKey()) {
            $this->error(true);
            return;
        }

        /** @var Blog_Model_Post $model */
        $model = App::getObject('model', 'blog_post');
        $post = $model->getByKey($this->getObjectKey(), App::getLanguage());

        if (!$post->getData('post_id')) {
            $this->error(true);
            return;
        }

        if (!$post->getData('status')) {
            $this->error(true);
            return;
        }

        $this->setCustom('post', $post);
        $this->setCustom('canonical', $post->getData('canonical'));
        $this->setCustom('meta_title', $post->getData('meta_title'));
        $this->setCustom('meta_description', $post->getData('meta_description'));
        $this->setCustom('breadcrumb', $this->getBreadcrumb($post));
        if ($post->getData('robots')) {
            $this->setCustom('robots', $post->getData('robots'));
        }
    }

    /**
     * Retrieve breadcrumb
     *
     * @param Leafiny_Object $post
     *
     * @return string[]
     */
    public function getBreadcrumb(Leafiny_Object $post): array
    {
        if ($this->getCustom('hide_breadcrumb')) {
            return [];
        }

        $links = [];

        if ($post->getData('breadcrumb')) {
            try {
                /** @var Category_Helper_Category $helper */
                $helper = App::getObject('helper', 'category');

                if ($helper instanceof Category_Helper_Category) {
                    $links = $helper->getBreadcrumb($post->getData('breadcrumb'));
                }
            } catch (Throwable $throwable) {
                return $links;
            }
        }

        $links[$post->getData('title')] = null;

        return $links;
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

    /**
     * Retrieve all images
     *
     * @param int $postId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getImages(int $postId): array
    {
        /** @var Gallery_Model_Image $gallery */
        $gallery = App::getObject('model', 'blog_post');

        return $gallery->getActivatedImages($postId, 'category');
    }
}
