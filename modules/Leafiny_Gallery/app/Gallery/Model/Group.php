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
 * Class Gallery_Model_Group
 */
class Gallery_Model_Group extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'gallery_group';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'group_id';

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

        $result = parent::save($object);

        $categories = $object->getData('category_ids');

        if ($result && $categories) {
            $this->saveCategories(
                $result,
                $categories instanceof Leafiny_Object ? $categories->getData() : $categories
            );
        }

        return $result;
    }

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool
     * @throws Exception
     */
    public function delete(int $id): bool
    {
        /** @var Gallery_Model_Image $model */
        $model = App::getObject('model', 'gallery_image');
        /** @var Core_Helper_File $helper */
        $helper = App::getObject('helper_file');

        $filters = [
            [
                'column'   => 'entity_id',
                'value'    => $id,
            ],
            [
                'column'   => 'entity_type',
                'value'    => $this->getObjectIdentifier(),
            ],
        ];

        $images = $model->getList($filters);
        foreach ($images as $image) {
            $helper->unlink($helper->getMediaDir() . $image->getData('image'));
        }

        $model->deleteEntityImages($id, $this->getObjectIdentifier());

        return parent::delete($id);
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

        $groupId = $object->getData($this->getPrimaryKey());
        if ($groupId) {
            $object->setData('category_ids', $this->getCategories($groupId));
        }

        return $object;
    }

    /**
     * Retrieve group by key and language
     *
     * @param string      $key
     * @param string|null $language
     *
     * @return Leafiny_Object
     */
    public function getByKey(string $key, ?string $language = null): Leafiny_Object
    {
        $object = new Leafiny_Object();

        try {
            $adapter = $this->getAdapter();
            if (!$adapter) {
                return $object;
            }

            if ($language) {
                $adapter->where('language', $language);
            }

            return $this->get($key, 'path_key');
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $object;
    }

    /**
     * Add category Filter
     *
     * @param int $categoryId
     *
     * @return Gallery_Model_Group
     * @throws Exception
     */
    public function addCategoryFilter(int $categoryId): Gallery_Model_Group
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return $this;
        }

        $adapter->join(
            'gallery_group_category as ggc',
            'main_table.group_id = ggc.group_id AND ggc.category_id = ' . $categoryId
        );

        return $this;
    }

    /**
     * Assign categories to gallery group
     *
     * @param int   $groupId
     * @param int[] $categories
     *
     * @return bool
     * @throws Exception
     */
    public function saveCategories(int $groupId, array $categories): bool
    {
        $this->getCategories($groupId);

        if (empty($categories)) {
            return false;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('group_id', $groupId);
        $adapter->delete('gallery_group_category');

        $data = [];

        foreach ($categories as $categoryId) {
            if (!$categoryId) {
                continue;
            }
            $data[] = [
                'group_id'    => $groupId,
                'category_id' => $categoryId,
            ];
        }

        return $adapter->insertMulti('gallery_group_category', $data) ? true : false;
    }

    /**
     * Retrieve gallery group categories
     *
     * @param int $groupId
     *
     * @return int[]
     * @throws Exception
     */
    public function getCategories(int $groupId): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('group_id', $groupId);
        $result = $adapter->get('gallery_group_category', null, ['category_id']);

        return array_column($result, 'category_id');
    }

    /**
     * Product validation
     *
     * @param Leafiny_Object $object
     *
     * @return string
     */
    public function validate(Leafiny_Object $object): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$object->getData('name')) {
            return 'The name cannot be empty';
        }
        if (!$object->getData('path_key')) {
            return 'The key cannot be empty';
        }

        return '';
    }
}
