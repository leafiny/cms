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
 * Class Cms_Page_Static_Content
 */
class Cms_Page_Static_Content extends Core_Page
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

        $page = $this->getCmsPage($this->getObjectKey());

        if (!$page) {
            $this->error(true);
            return;
        }

        if (!$page->getData('status')) {
            $this->error(true);
            return;
        }

        $this->setCustom('page', $page);
        $this->setCustom('canonical', $page->getData('canonical'));
        $this->setCustom('meta_title', $page->getData('meta_title'));
        $this->setCustom('meta_description', $page->getData('meta_description'));
        $this->setCustom('inline_css', $page->getData('inline_css'));
        $this->setCustom('breadcrumb', $this->getBreadcrumb($page));
        if ($page->getData('robots')) {
            $this->setCustom('robots', $page->getData('robots'));
        }
    }

    /**
     * Retrieve CMS Page
     *
     * @param string $pathKey
     *
     * @return Leafiny_Object|null
     */
    protected function getCmsPage(string $pathKey): ?Leafiny_Object
    {
        /** @var Cms_Model_Page $model */
        $model = App::getObject('model', 'cms_page');
        $page = $model->getByKey($pathKey, App::getLanguage());

        if (!$page->getData('page_id')) {
            return null;
        }

        return $page;
    }

    /**
     * Retrieve breadcrumb
     *
     * @param Leafiny_Object $page
     *
     * @return string[]
     */
    public function getBreadcrumb(Leafiny_Object $page): array
    {
        if ($this->getCustom('hide_breadcrumb')) {
            return [];
        }

        $links = [];

        if ($page->getData('breadcrumb')) {
            try {
                /** @var Category_Helper_Category $helper */
                $helper = App::getObject('helper', 'category');

                if ($helper instanceof Category_Helper_Category) {
                    $links = $helper->getBreadcrumb($page->getData('breadcrumb'));
                }
            } catch (Throwable $throwable) {
                App::log($throwable, Core_Interface_Log::ERR);
                return $links;
            }
        }

        $links[$page->getData('title')] = null;

        return $links;
    }

    /**
     * Retrieve main image
     *
     * @param int $pageId
     *
     * @return Leafiny_Object|null
     */
    public function getMainImage(int $pageId): ?Leafiny_Object
    {
        /** @var Gallery_Model_Image $image */
        $image = App::getObject('model', 'gallery_image');

        try {
            return $image->getMainImage($pageId, 'cms_page');
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return null;
    }

    /**
     * Retrieve all images
     *
     * @param int $pageId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getImages(int $pageId): array
    {
        /** @var Gallery_Model_Image $gallery */
        $gallery = App::getObject('model', 'cms_page');

        return $gallery->getActivatedImages($pageId, 'category');
    }
}
