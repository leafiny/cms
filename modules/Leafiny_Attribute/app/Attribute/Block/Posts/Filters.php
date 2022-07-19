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
 * Class Attribute_Block_Posts_Filters
 */
class Attribute_Block_Posts_Filters extends Attribute_Block_Filters
{
    /**
     * Retrieve entity type name
     *
     * @return string
     */
    public function getEntityType(): string
    {
        return 'blog_post';
    }

    /**
     * Retrieve total of filtered items in the category
     *
     * @param int|null $categoryId
     *
     * @return int|null
     * @throws Exception
     */
    public function getTotalItems(?int $categoryId = null): ?int
    {
        /** @var Blog_Helper_Blog_Post $helper */
        $helper = App::getSingleton('helper', 'blog_post');

        return $helper->getTotalCategoryPosts($categoryId);
    }

    /**
     * Retrieve all item identifiers in the category
     *
     * @param int|null $categoryId
     *
     * @return int[]
     * @throws Exception
     */
    public function getItemIds(?int $categoryId = null): array
    {
        if ($this->getData('_item_ids')) {
            return $this->getData('_item_ids');
        }

        /** @var Blog_Helper_Blog_Post $helper */
        $helper = App::getSingleton('helper', 'blog_post');
        /** @var Blog_Model_Post $model */
        $model = App::getSingleton('model', 'blog_post');

        if ($categoryId) {
            $model->addCategoryFilter($categoryId);
        }

        $posts = $model->getList($helper->getFilters(), [], null, [], ['main_table.post_id']);

        $postIds = [];
        foreach ($posts as $post) {
            $postIds[] = (int)$post->getData('post_id');
        }

        $this->setData('_item_ids', $postIds);

        return $this->getData('_item_ids');
    }
}
