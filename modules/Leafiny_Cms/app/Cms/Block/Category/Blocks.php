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
 * Class Cms_Block_Category_Blocks
 */
class Cms_Block_Category_Blocks extends Core_Block
{
    /**
     * Retrieve blocks
     *
     * @param int $categoryId
     *
     * @return array
     * @throws Exception
     */
    public function getBlocks(int $categoryId): array
    {
        /** @var Cms_Model_Block $model */
        $model = App::getObject('model', 'cms_block');

        $filters = [
            [
                'column'   => 'status',
                'value'    => 1,
                'operator' => '=',
            ],
        ];

        $orders = [
            [
                'order' => 'created_at',
                'dir'   => 'DESC',
            ]
        ];

        $blocks = $model->addCategoryFilter($categoryId)->getList($filters, $orders);

        /** @var Cms_Helper_Cms $helper */
        $helper = App::getObject('helper', 'cms');

        foreach ($blocks as $block) {
            $helper->secureChildBlocks($block);
        }

        return $blocks;
    }
}
