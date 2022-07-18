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
 * Class Attribute_Block_Filters
 */
abstract class Attribute_Block_Filters extends Core_Block
{
    /**
     * Retrieve entity type name
     *
     * @return string
     */
    abstract public function getEntityType(): string;

    /**
     * Are there any items in category?
     *
     * @param int $categoryId
     *
     * @return bool
     */
    abstract public function hasItems(int $categoryId): bool;

    /**
     * Retrieve item identifiers
     *
     * @param int|null $categoryId
     *
     * @return int[]
     */
    abstract public function getItemIds(?int $categoryId = null): array;

    /**
     * Retrieve current filters
     *
     * @param Core_Page $page
     *
     * @return Leafiny_Object[]
     */
    public function getFiltersApplied(Core_Page $page): array
    {
        $filters = $page->getParams(['get']);
        if (empty($filters)) {
            return [];
        }

        /** @var Attribute_Model_Attribute $attributeModel */
        $attributeModel = App::getSingleton('model', 'attribute');
        $current = [];

        try {
            foreach ($filters->getData() as $code => $options) {
                if (!($options instanceof Leafiny_Object)) {
                    $options = new Leafiny_Object([(int)$options]);
                }
                $filter = $attributeModel->get($code, 'code');
                if (!$filter->getData($attributeModel->getPrimaryKey())) {
                    continue;
                }
                $applied = [];
                foreach ($options->getData() as $optionId) {
                    $option = $attributeModel->getOption((int)$optionId);
                    if (!$option->hasData()) {
                        continue;
                    }
                    if (!isset($filter->getData('options')[(int)$optionId])) {
                        continue;
                    }
                    $applied[(int)$optionId] = $option;
                }
                $filter->setData('applied_options', $applied);

                $current[$code] = $filter;
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $current;
    }

    /**
     * Retrieve current page URL
     *
     * @param Core_Page $page
     *
     * @return string
     */
    public function getPageUrl(Core_Page $page): string
    {
        return App::getUrlRewrite($page->getObjectKey(), 'category') . '#filters';
    }

    /**
     * Retrieve filters
     *
     * @param int|null $categoryId
     *
     * @return array
     */
    public function getFilters(?int $categoryId = null): array
    {
        /** @var Attribute_Model_Attribute $attributeModel */
        $attributeModel = App::getSingleton('model', 'attribute');

        try {
            return $attributeModel->getFilterableEntityAttributes(
                $this->getItemIds($categoryId),
                $this->getEntityType(),
                App::getLanguage()
            );
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }
}
