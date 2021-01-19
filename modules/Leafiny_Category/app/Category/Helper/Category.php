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
 * Class Category_Helper_Category
 */
class Category_Helper_Category extends Core_Helper
{
    /**
     * Retrieve category breadcrumb
     *
     * @param int      $categoryId
     * @param string[] $categories
     *
     * @return string[]
     */
    public function getBreadcrumb(int $categoryId, array &$categories = []): array
    {
        /** @var Category_Model_Category $model */
        $model = App::getObject('model', 'category');

        try {
            $category = $model->get($categoryId);
            if ($category->hasData()) {
                $categories[$category->getData('name')] = App::getBaseUrl() . $category->getData('path_key') . '.html';
                if ($category->getData('parent_id')) {
                    $this->getBreadcrumb($category->getData('parent_id'), $categories);
                }
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return array_reverse($categories);
    }
}
