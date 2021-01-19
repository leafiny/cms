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
 * Class Blog_Model_Post
 */
class Blog_Model_Post extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'blog_post';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'post_id';

    /**
     * Retrieve post by key and language
     *
     * @param string $key
     * @param string $language
     *
     * @return Leafiny_Object
     */
    public function getByKey(string $key, string $language = 'en_US'): Leafiny_Object
    {
        $object = new Leafiny_Object();

        try {
            $adapter = $this->getAdapter();
            if (!$adapter) {
                return $object;
            }

            $adapter->where('path_key', $key);
            $adapter->where('language', $language);

            $result = $adapter->getOne($this->getMainTable());
            if ($result) {
                $object->setData($result);
            }

            App::dispatchEvent($this->getObjectIdentifier() . '_get_after', ['object' => $object]);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $object;
    }

    /**
     * Retrieve data
     *
     * @param mixed $value
     * @param string|null $column
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function get($value, ?string $column = null): Leafiny_Object
    {
        $object = parent::get($value, $column);

        $postId = $object->getData($this->getPrimaryKey());
        if ($postId) {
            $object->setData('category_ids', $this->getCategories($postId));
        }

        return $object;
    }

    /**
     * Save
     *
     * @param Leafiny_Object $object
     *
     * @return int|null
     * @throws Exception
     */
    public function save(Leafiny_Object $object): ?int
    {
        if ($object->getData('path_key')) {
            /** @var Core_Helper $helper */
            $helper = App::getObject('helper');
            $object->setData('path_key', $helper->formatKey($object->getData('path_key')));
        }

        /** @var Leafiny_Object|int[]|null $categories */
        $categories = null;
        if ($object->getData('category_ids')) {
            $categories = $object->getData('category_ids');
        }

        $result = parent::save($object);

        if ($result) {
            /** @var Rewrite_Model_Rewrite $rewrite */
            $rewrite = App::getObject('model', 'rewrite');
            if (method_exists($rewrite, 'refreshAll')) {
                $rewrite->refreshAll('blog_post');
            }
        }

        if ($result && $categories) {
            $this->saveCategories(
                $result,
                $categories instanceof Leafiny_Object ? $categories->getData() : $categories
            );
        }

        return $result;
    }

    /**
     * Add category Filter
     *
     * @param int $categoryId
     *
     * @return Blog_Model_Post
     * @throws Exception
     */
    public function addCategoryFilter(int $categoryId): Blog_Model_Post
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return $this;
        }

        $adapter->join(
            'blog_post_category as bpc',
            'main_table.post_id = bpc.post_id AND bpc.category_id = ' . $categoryId
        );

        return $this;
    }

    /**
     * Assign categories to blog post
     *
     * @param int   $postId
     * @param int[] $categories
     *
     * @return bool
     * @throws Exception
     */
    public function saveCategories(int $postId, array $categories): bool
    {
        $this->getCategories($postId);

        if (empty($categories)) {
            return false;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('post_id', $postId);
        $adapter->delete('blog_post_category');

        $data = [];

        foreach ($categories as $categoryId) {
            if (!$categoryId) {
                continue;
            }
            $data[] = [
                'post_id'     => $postId,
                'category_id' => $categoryId,
            ];
        }

        return $adapter->insertMulti('blog_post_category', $data) ? true : false;
    }

    /**
     * Retrieve post categories
     *
     * @param int $postId
     *
     * @return int[]
     * @throws Exception
     */
    public function getCategories(int $postId): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('post_id', $postId);
        $result = $adapter->get('blog_post_category', null, ['category_id']);

        return array_column($result, 'category_id');
    }

    /**
     * Add comment to post
     *
     * @param int $postId
     * @param int $commentId
     *
     * @return bool
     * @throws Exception
     */
    public function addComment(int $postId, int $commentId): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        return $adapter->insert(
            'blog_post_comment',
            [
                'post_id'    => $postId,
                'comment_id' => $commentId,
            ]
        );
    }

    /**
     * Retrieve post comments
     *
     * @param int $postId
     *
     * @return int[]
     * @throws Exception
     */
    public function getComments(int $postId): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('post_id', $postId);
        $result = $adapter->get('blog_post_comment', null, ['comment_id']);

        return array_column($result, 'comment_id');
    }

    /**
     * Object validation
     *
     * @param Leafiny_Object $form
     *
     * @return string
     */
    public function validate(Leafiny_Object $form): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$form->getData('path_key')) {
            return 'The key cannot be empty';
        }

        return '';
    }
}
