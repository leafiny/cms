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
 * Class Gallery_Helper_Gallery_Group
 */
class Gallery_Helper_Gallery_Group extends Core_Helper
{
    /**
     * Retrieve Category Groups
     *
     * @param int $categoryId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getCategoryGroups(int $categoryId): array
    {
        /** @var Gallery_Model_Group $groupModel */
        $groupModel = App::getSingleton('model', 'gallery_group');

        return $groupModel->addCategoryFilter($categoryId)
            ->getList($this->getFilters(), $this->getSortOrder(), null, $this->getJoins(), $this->getColumns());
    }

    /**
     * Retrieve gallery group filters
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

        return array_merge($filters, $this->getCustom('group_filters') ?: []);
    }

    /**
     * Retrieve sort order
     *
     * @return array
     */
    public function getSortOrder(): array
    {
        return $this->getCustom('list_sort_order') ?: [];
    }

    /**
     * Retrieve join list
     *
     * @return array
     */
    public function getJoins(): array
    {
        return $this->getCustom('list_joins') ?: [];
    }

    /**
     * Retrieve columns
     *
     * @return array
     */
    public function getColumns(): array
    {
        return $this->getCustom('list_columns') ?: [];
    }
}
