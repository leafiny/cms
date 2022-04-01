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
 * Class Catalog_Model_Product
 */
class Catalog_Model_Product extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'catalog_product';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'product_id';

    /**
     * Retrieve product by key and language
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

            $adapter->where('language', $language);

            return $this->get($key, 'path_key');
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

        $productId = $object->getData($this->getPrimaryKey());
        if ($productId) {
            $object->setData('category_ids', $this->getCategories($productId));
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
            $object->setData('path_key', $helper->formatKey($object->getData('path_key'), [], ['/']));
        }
        if ($object->getData('weight')) {
            $object->setData('weight', (float)str_replace(',', '.', $object->getData('weight')));
        }
        if ($object->getData('price')) {
            $object->setData('price', (float)str_replace(',', '.', $object->getData('price')));
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
                $rewrite->refreshAll('catalog_product');
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
     * @return Catalog_Model_Product
     * @throws Exception
     */
    public function addCategoryFilter(int $categoryId): Catalog_Model_Product
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return $this;
        }

        $adapter->join(
            'catalog_product_category as ccp',
            'main_table.product_id = ccp.product_id AND ccp.category_id = ' . $categoryId
        );

        return $this;
    }

    /**
     * Assign categories to product
     *
     * @param int $productId
     * @param int[] $categories
     *
     * @return bool
     * @throws Exception
     */
    public function saveCategories(int $productId, array $categories): bool
    {
        $this->getCategories($productId);

        if (empty($categories)) {
            return false;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('product_id', $productId);
        $adapter->delete('catalog_product_category');

        $data = [];

        foreach ($categories as $categoryId) {
            if (!$categoryId) {
                continue;
            }
            $data[] = [
                'product_id'  => $productId,
                'category_id' => $categoryId,
            ];
        }

        return $adapter->insertMulti('catalog_product_category', $data) ? true : false;
    }

    /**
     * Retrieve product categories
     *
     * @param int $productId
     *
     * @return int[]
     * @throws Exception
     */
    public function getCategories(int $productId): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('product_id', $productId);
        $result = $adapter->get('catalog_product_category', null, ['category_id']);

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

        if (!$object->getData('sku')) {
            return 'The sku cannot be empty';
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
