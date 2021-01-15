<?php

declare(strict_types=1);

/**
 * Class Cms_Model_Page
 */
class Cms_Model_Page extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'cms_page';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'page_id';

    /**
     * Retrieve page by key and language
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

        $pageId = $object->getData($this->getPrimaryKey());
        if ($pageId) {
            $object->setData('category_ids', $this->getCategories($pageId));
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
                $rewrite->refreshAll('cms_page');
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
     * @return Cms_Model_Page
     * @throws Exception
     */
    public function addCategoryFilter(int $categoryId): Cms_Model_Page
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return $this;
        }

        $adapter->join(
            'cms_page_category as ccp',
            'main_table.page_id = ccp.page_id AND ccp.category_id = ' . $categoryId
        );

        return $this;
    }

    /**
     * Assign categories to cms page
     *
     * @param int $pageId
     * @param int[] $categories
     *
     * @return bool
     * @throws Exception
     */
    public function saveCategories(int $pageId, array $categories): bool
    {
        $this->getCategories($pageId);

        if (empty($categories)) {
            return false;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('page_id', $pageId);
        $adapter->delete('cms_page_category');

        $data = [];

        foreach ($categories as $categoryId) {
            if (!$categoryId) {
                continue;
            }
            $data[] = [
                'page_id'     => $pageId,
                'category_id' => $categoryId,
            ];
        }

        return $adapter->insertMulti('cms_page_category', $data) ? true : false;
    }

    /**
     * Retrieve page categories
     *
     * @param int $pageId
     *
     * @return int[]
     * @throws Exception
     */
    public function getCategories(int $pageId): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('page_id', $pageId);
        $result = $adapter->get('cms_page_category', null, ['category_id']);

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
