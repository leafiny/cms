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
 * Class Cms_Model_Block
 */
class Cms_Model_Block extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'cms_block';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'block_id';

    /**
     * Retrieve block by key and language
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

        $blockId = $object->getData($this->getPrimaryKey());
        if ($blockId) {
            $object->setData('category_ids', $this->getCategories($blockId));
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
     * @return Cms_Model_Block
     * @throws Exception
     */
    public function addCategoryFilter(int $categoryId): Cms_Model_Block
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return $this;
        }

        $adapter->join(
            'cms_block_category as ccp',
            'main_table.block_id = ccp.block_id AND ccp.category_id = ' . $categoryId
        );

        return $this;
    }

    /**
     * Assign categories to cms block
     *
     * @param int $blockId
     * @param int[] $categories
     *
     * @return bool
     * @throws Exception
     */
    public function saveCategories(int $blockId, array $categories): bool
    {
        $this->getCategories($blockId);

        if (empty($categories)) {
            return false;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('block_id', $blockId);
        $adapter->delete('cms_block_category');

        $data = [];

        foreach ($categories as $categoryId) {
            if (!$categoryId) {
                continue;
            }
            $data[] = [
                'block_id'    => $blockId,
                'category_id' => $categoryId,
            ];
        }

        return $adapter->insertMulti('cms_block_category', $data) ? true : false;
    }

    /**
     * Retrieve block categories
     *
     * @param int $blockId
     *
     * @return int[]
     * @throws Exception
     */
    public function getCategories(int $blockId): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('block_id', $blockId);
        $result = $adapter->get('cms_block_category', null, ['category_id']);

        return array_column($result, 'category_id');
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
