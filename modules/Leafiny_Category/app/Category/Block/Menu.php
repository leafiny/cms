<?php

declare(strict_types=1);

/**
 * Class Category_Block_Menu
 */
class Category_Block_Menu extends Core_Block
{
    /**
     * Retrieve category tree
     *
     * @return array
     */
    public function getTree(): array
    {
        /** @var Category_Model_Category $model */
        $model = App::getSingleton('model', 'category');

        try {
            $filters = [
                ['column' => 'status', 'value' => 1, 'operator' => '='],
                ['column' => 'show_in_menu', 'value' => 1, 'operator' => '='],
            ];

            return $model->getTree(App::getLanguage(), $filters);
        } catch (Exception $exception) {
            /** @var Log_Model_File $log */
            $log = App::getObject('model', 'log_file');
            $log->add($exception->getMessage());
        }

        return [];
    }

    /**
     * Retrieve if category is active
     *
     * @param string $key
     *
     * @return bool
     */
    public function isActive(string $key): bool
    {
        if ($this->getParentObjectIdentifier() === '/' . $key . '.html') {
            return true;
        }

        if ($this->getParentObjectKey() === $key) {
            return true;
        }

        return false;
    }
}
