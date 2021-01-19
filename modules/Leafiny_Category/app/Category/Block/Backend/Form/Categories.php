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
 * Class Category_Block_Backend_Form_Categories
 */
class Category_Block_Backend_Form_Categories extends Core_Block
{
    /**
     * Retrieve if select is a multi-select
     *
     * @return bool
     */
    public function isMultiple(): bool
    {
        return (bool)$this->getCustom('multiple');
    }

    /**
     * Retrieve select name
     *
     * @return string
     */
    public function getName(): string
    {
        return (string)$this->getCustom('name');
    }

    /**
     * Retrieve Label
     *
     * @return string
     */
    public function getLabel(): string
    {
        return (string)$this->getCustom('label');
    }

    /**
     * Retrieve all categories
     *
     * @param string       $language
     * @param int|null     $currentId
     * @param array[]|null $tree
     * @param string       $indent
     *
     * @return array[]
     * @throws Exception
     */
    public function getCategories(string $language, ?int $currentId, ?array $tree = null, string $indent = ''): array
    {
        if ($tree === null) {
            $tree = $this->getTree($language);
        }

        $options = [];

        foreach ($tree as $category) {
            $name = $category['name'];
            if ($category['category_id'] === $currentId) {
                $name = '(' . $name . ')';
            }
            $options[] = [
                'value' => $category['category_id'],
                'label' => trim($indent . ' ' . $name)
            ];

            if (isset($category['children'])) {
                $options = array_merge(
                    $options,
                    $this->getCategories($language, $currentId, $category['children'], $indent . '---')
                );
            }
        }

        return $options;
    }

    /**
     * Retrieve tree by language
     *
     * @var string $language
     *
     * @return array[]
     * @throws Exception
     */
    public function getTree(string $language): array
    {
        /** @var Category_Model_Category $model */
        $model = App::getObject('model', 'category');

        return $model->getTree($language);
    }
}
