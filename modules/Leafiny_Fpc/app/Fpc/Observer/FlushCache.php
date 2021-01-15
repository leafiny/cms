<?php

declare(strict_types=1);

/**
 * Class Fpc_Observer_FlushCache
 */
class Fpc_Observer_FlushCache extends Core_Event implements Core_Interface_Event
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object $entity */
        $entity = $object->getData('object');
        /** @var Fpc_Helper_Cache $helper */
        $helper = App::getObject('helper', 'fpc_cache');

        if ($entity->getData('path_key')) {
            $helper->flushCache($entity->getData('path_key'));
        }

        if (class_exists('Category_Model_Category') && $entity->getData('category_ids')) {
            $categories = $entity->getData('category_ids');

            if ($categories instanceof Leafiny_Object) {
                $categories = $categories->getData();
            }

            if (is_array($categories)) {
                /** @var Category_Model_Category $model */
                $model = App::getObject('model', 'category');

                foreach ($categories as $categoryId) {
                    try {
                        $category = $model->get($categoryId);
                        $helper->flushCache($category->getData('path_key'));
                    } catch (Throwable $throwable) {}
                }
            }
        }
    }
}
