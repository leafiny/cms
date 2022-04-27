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
 * Class Cms_Block_Search_Blocks
 */
class Cms_Block_Search_Blocks extends Core_Block
{
    /**
     * Retrieve blocks
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getBlocks(): array
    {
        if (!$this->getBlockIds()) {
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
                'column'   => 'block_id',
                'value'    => $this->getBlockIds(),
                'operator' => 'IN',
            ],
        ];

        $orders = [
            [
                'order'  => 'block_id',
                'dir'    => 'ASC',
                'custom' => $this->getBlockIds(),
            ]
        ];

        /** @var Cms_Model_Block $model */
        $model = App::getObject('model', 'cms_block');

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
    public function getBlockIds(): array
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
