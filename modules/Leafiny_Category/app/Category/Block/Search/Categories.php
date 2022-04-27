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
 * Class Category_Block_Search_Categories
 */
class Category_Block_Search_Categories extends Core_Block
{
    /**
     * Retrieve categories
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getCategories(): array
    {
        if (!$this->getCategoryIds()) {
            return [];
        }

        $filters = [
            [
                'column' => 'status',
                'value'  => 1,
            ],
            [
                'column' => 'language',
                'value'  => App::getLanguage(),
            ],
            [
                'column'   => 'category_id',
                'value'    => $this->getCategoryIds(),
                'operator' => 'IN',
            ],
        ];

        $orders = [
            [
                'order'  => 'category_id',
                'dir'    => 'ASC',
                'custom' => $this->getCategoryIds(),
            ]
        ];

        /** @var Category_Model_Category $model */
        $model = App::getObject('model', 'category');

        return $model->getList($filters, $orders, $this->getLimit());
    }

    /**
     * Retrieve limit
     *
     * @return int[]
     */
    public function getLimit(): array
    {
        return $this->getCustom('limit') ?? [0, 100];
    }

    /**
     * Retrieve object Ids
     *
     * @return array
     */
    public function getCategoryIds(): array
    {
        if (!$this->getResult()) {
            return [];
        }

        return $this->getResult()->getData('response') ?? [];
    }

    /**
     * Retrieve search result
     *
     * @return Leafiny_Object|null
     */
    public function getResult(): ?Leafiny_Object
    {
        return $this->getCustom('result');
    }
}
