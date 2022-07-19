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
     * Retrieve total of filtered items in the category
     *
     * @param int|null $categoryId
     *
     * @return int|null
     */
    abstract public function getTotalItems(?int $categoryId): ?int;

    /**
     * Retrieve all item identifiers in the category
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
        if ($this->getData('_filters_applied')) {
            return $this->getData('_filters_applied');
        }

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
                    $option->setData(
                        'option_label',
                        $option->getData('label')[$page->getLanguage(true)] ?? null
                    );
                    $applied[(int)$optionId] = $option;
                }
                $filter->setData('applied_options', $applied);

                $filter->setData(
                    'attribute_label',
                    $filter->getData('label')[$page->getLanguage(true)] ?? null
                );

                $current[$code] = $filter;
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        $this->setData('_filters_applied', $current);

        return $this->getData('_filters_applied');
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
     * Retrieve filtered URL
     *
     * @param Core_Page $page
     * @param string    $attributeCode
     * @param int       $optionId
     * @param string    $action
     *
     * @return string
     */
    public function getFilteredUrl(Core_Page $page, string $attributeCode, int $optionId, string $action = 'add'): string
    {
        $url = App::getUrlRewrite($page->getObjectKey(), 'category');

        $params = $page->getParams(['get']);

        if ($action === 'add') {
            $options = [];
            if ($params->getData($attributeCode)) {
                $options = $params->getData($attributeCode);
                if (!($options instanceof Leafiny_Object)) {
                    $options = new Leafiny_Object([(int)$options]);
                }
                $options = $options->getData();
            }
            $options[] = $optionId;
            $options = array_values(array_unique($options));
            sort($options);
            $params->setData($attributeCode, $options);
        }

        if ($action === 'remove') {
            foreach ($params->getData() as $code => $options) {
                if (!($options instanceof Leafiny_Object)) {
                    continue;
                }
                $options = $options->getData();
                if ($code === $attributeCode) {
                    foreach ($options as $key => $id) {
                        if ($optionId !== (int)$id) {
                            continue;
                        }
                        unset($options[$key]);
                    }
                }
                $options = array_values(array_unique($options));
                sort($options);
                $params->setData($code, $options);
            }
        }

        return $url . '?' . http_build_query($params->getData()) . '#filters';
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
        if ($this->getData('_filters')) {
            return $this->getData('_filters');
        }

        $this->setData('_filters', []);

        /** @var Attribute_Model_Attribute $attributeModel */
        $attributeModel = App::getSingleton('model', 'attribute');

        try {
            $filters = $attributeModel->getFilterableEntityAttributes(
                $this->getItemIds($categoryId),
                $this->getEntityType(),
                App::getLanguage()
            );
            $this->setData('_filters', $filters);
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $this->getData('_filters');
    }

    /**
     * Retie option hex color
     *
     * @param Leafiny_Object $option
     *
     * @return string|null
     */
    public function getColor(Leafiny_Object $option): ?string
    {
        $custom = $option->getData('custom');

        if (!$custom) {
            return null;
        }
        if (!preg_match('/#([a-f0-9]{3}){1,2}\b/i', $custom)) {
            return null;
        }

        return $custom;
    }
}
