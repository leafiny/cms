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
 * Class Cms_Helper_Cms_Block
 */
class Cms_Helper_Cms_Block extends Core_Helper
{
    /**
     * Secure child blocks
     *
     * @param Leafiny_Object $block
     */
    public function secureChildBlocks(Leafiny_Object $block): void
    {
        $content = $block->getData('content');

        if (!$content) {
            return;
        }

        $identifier = 'block.static::' . $block->getData('path_key');
        $pattern = '/{{\s?child\((\'|")' . $identifier . '(\'|")\)\s?}}/';

        if (!preg_match($pattern, $content)) {
            return;
        }

        $content = preg_replace($pattern, '', $content);

        $block->setData('content', $content);
    }

    /**
     * Retrieve Category blocks
     *
     * @param int $categoryId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getCategoryBlocks(int $categoryId): array
    {
        /** @var Cms_Model_Block $model */
        $model = App::getObject('model', 'cms_block');

        $blocks = $model->addCategoryFilter($categoryId)
            ->getList($this->getFilters(), $this->getSortOrder(), null, $this->getJoins(), $this->getColumns());

        foreach ($blocks as $block) {
            $this->secureChildBlocks($block);
        }

        return $blocks;
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

        return array_merge($filters, $this->getCustom('block_filters') ?: []);
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
                'order' => 'position',
                'dir'   => 'ASC',
            ],
        ];

        return $this->getCustom('list_sort_order') ?: $sortOrder;
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
