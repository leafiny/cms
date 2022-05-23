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
 * Class Cms_Helper_Cms_Page
 */
class Cms_Helper_Cms_Page extends Core_Helper
{
    /**
     * Retrieve Category pages
     *
     * @param int $categoryId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getCategoryPages(int $categoryId): array
    {
        /** @var Cms_Model_Page $model */
        $model = App::getObject('model', 'cms_page');

        return $model->addCategoryFilter($categoryId)
            ->getList($this->getFilters(), $this->getSortOrder(), null, $this->getJoins());
    }

    /**
     * Retrieve filters
     *
     * @return array[]
     */
    public function getFilters(): array
    {
        $filters = [
            'status' => [
                'column' => 'status',
                'value'  => 1,
            ],
            'language' => [
                'column' => 'language',
                'value'  => App::getLanguage(),
            ]
        ];

        return array_merge($filters, $this->getCustom('page_filters') ?: []);
    }

    /**
     * Retrieve sort order
     *
     * @return string[][]
     */
    public function getSortOrder(): array
    {
        $sortOrder = [
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ],
        ];

        return $this->getCustom('page_sort_order') ?: $sortOrder;
    }

    /**
     * Retrieve join list
     *
     * @return array
     */
    public function getJoins(): array
    {
        return $this->getCustom('page_joins') ?: [];
    }
}
