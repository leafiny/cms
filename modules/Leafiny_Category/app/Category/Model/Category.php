<?php

declare(strict_types=1);

/**
 * Class Category_Model_Category
 */
class Category_Model_Category extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'category';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'category_id';

    /**
     * Retrieve category by key and language
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
        } catch (Exception $exception) {
            /** @var Log_Model_File $log */
            $log = App::getObject('model', 'log_file');
            $log->add($exception->getMessage());
        }

        return $object;
    }

    /**
     * Retrieve category list by language
     *
     * @param string $language
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getListByLanguage(string $language): array
    {
        $filters[] = [
            'column'   => 'language',
            'value'    => $language,
            'operator' => '='
        ];

        return $this->getList($filters);
    }

    /**
     * Retrieve category tree
     *
     * @param string|null $language
     * @param array[]     $filters
     *
     * @return array
     * @throws Exception
     */
    public function getTree(?string $language = null, array $filters = []): array
    {
        if ($language) {
            $filters[] = [
                'column'   => 'language',
                'value'    => $language,
                'operator' => '='
            ];
        }

        $categories = $this->getList($filters);

        $refs = [];
        $list = [];

        foreach ($categories as $category) {
            $categoryId = $category->getData('category_id');

            $ref = &$refs[$categoryId];

            $object = new Leafiny_Object();
            $object->setData(
                [
                    'category_id' => $categoryId,
                    'name'        => $category->getData('name'),
                    'path_key'    => $category->getData('path_key'),
                    'position'    => $category->getData('position'),
                    'language'    => $category->getData('language'),
                    'status'      => $category->getData('status'),
                ]
            );
            App::dispatchEvent('category_tree_set_ref', ['ref' => $object, 'category' => $category]);

            $ref = array_merge($ref ?: [], $object->getData());

            if (!$category->getData('parent_id')) {
                $list[$categoryId] = &$ref;
            } else {
                $refs[$category->getData('parent_id')]['children'][$categoryId] = &$ref;
            }
        }

        return $this->sortTreeByPosition($list);
    }

    /**
     * Sort category tree by position
     *
     * @param array[] $categories
     *
     * @return array[]
     */
    protected function sortTreeByPosition(array $categories): array
    {
        $position = array_column($categories, 'position');

        if (!empty($position)) {
            array_multisort($position, SORT_ASC, $categories);
        }

        foreach ($categories as $id => $category) {
            if (isset($category['children'])) {
                $categories[$id]['children'] = $this->sortTreeByPosition($category['children']);
            }
        }

        return $categories;
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

        $result = parent::save($object);

        if ($result) {
            /** @var Rewrite_Model_Rewrite $rewrite */
            $rewrite = App::getObject('model', 'rewrite');
            if (method_exists($rewrite, 'refreshAll')) {
                $rewrite->refreshAll('category');
            }
        }

        return $result;
    }

    /**
     * Object validation
     *
     * @param Leafiny_Object $category
     *
     * @return string
     */
    public function validate(Leafiny_Object $category): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$category->getData('name')) {
            return 'The name cannot be empty';
        }
        if (!$category->getData('path_key')) {
            return 'The key cannot be empty';
        }
        if ($category->getData($this->getPrimaryKey())) {
            if ($category->getData($this->getPrimaryKey()) === $category->getData('parent_id')) {
                return 'Category parent cannot be the same as the category';
            }
        }

        return '';
    }
}
