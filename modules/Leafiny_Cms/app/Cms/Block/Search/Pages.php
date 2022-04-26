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
 * Class Cms_Block_Search_Pages
 */
class Cms_Block_Search_Pages extends Core_Block
{
    /**
     * Retrieve pages
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getPages(): array
    {
        if (!$this->getPageIds()) {
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
                'column'   => 'page_id',
                'value'    => $this->getPageIds(),
                'operator' => 'IN',
            ],
        ];

        $orders = [
            [
                'order'  => 'page_id',
                'dir'    => 'ASC',
                'custom' => $this->getPageIds(),
            ]
        ];

        /** @var Cms_Model_Page $model */
        $model = App::getObject('model', 'cms_page');

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
    public function getPageIds(): array
    {
        return $this->getCustom('response') ?? [];
    }
}
