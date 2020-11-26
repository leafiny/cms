<?php

declare(strict_types=1);

/**
 * Class Category_Page_Category_View
 */
class Category_Page_Category_View extends Core_Page
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

        /** @var Category_Model_Category $model */
        $model = App::getObject('model', 'category');
        $category = $model->getByKey($this->getObjectKey(), App::getLanguage());

        if (!$category->getData('category_id')) {
            $this->error(true);
            return;
        }

        if (!$category->getData('status')) {
            $this->error(true);
            return;
        }

        $this->setCustom('category', $category);
        $this->setCustom('canonical', $category->getData('canonical'));
        $this->setCustom('meta_title', $category->getData('meta_title'));
        $this->setCustom('meta_description', $category->getData('meta_description'));
        $this->setCustom('breadcrumb', $this->getBreadcrumb($category));
        if ($category->getData('robots')) {
            $this->setCustom('robots', $category->getData('robots'));
        }
    }

    /**
     * Retrieve breadcrumb
     *
     * @param Leafiny_Object $category
     *
     * @return string[]
     */
    public function getBreadcrumb(Leafiny_Object $category): array
    {
        if ($this->getCustom('hide_breadcrumb')) {
            return [];
        }

        /** @var Category_Helper_Category $helper */
        $helper = App::getObject('helper', 'category');

        return $helper->getBreadcrumb($category->getData('category_id'));
    }

    /**
     * Retrieve Children blocks
     *
     * @return string[]
     */
    public function getChildren(): array
    {
        $children = $this->getCustom('children');

        if (!$children) {
            return [];
        }

        ksort($children);

        return $children;
    }

    /**
     * Retrieve category main image
     *
     * @param int $categoryId
     *
     * @return Leafiny_Object|null
     */
    public function getMainImage(int $categoryId): ?Leafiny_Object
    {
        /** @var Gallery_Model_Image $image */
        $image = App::getObject('model', 'gallery_image');

        try {
            return $image->getMainImage($categoryId, 'category');
        } catch (Exception $exception) {
            /** @var Log_Model_File $log */
            $log = App::getObject('model', 'log_file');
            $log->add($exception->getMessage());
        }

        return null;
    }

    /**
     * Retrieve all category images
     *
     * @param int $categoryId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getImages(int $categoryId): array
    {
        /** @var Gallery_Model_Image $gallery */
        $gallery = App::getObject('model', 'gallery_image');

        return $gallery->getActivatedImages($categoryId, 'category');
    }
}
