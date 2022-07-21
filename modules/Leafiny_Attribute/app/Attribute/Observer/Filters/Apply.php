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
 * Class Attribute_Observer_Filters_Apply
 */
class Attribute_Observer_Filters_Apply extends Core_Observer implements Core_Interface_Observer
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
        /** @var Core_Page $page */
        $page = $object->getData('object');

        if ($page->getObjectIdentifier() !== '/category/*.html') {
            return;
        }

        /** @var Attribute_Helper_Attribute $attributeHelper */
        $attributeHelper = App::getSingleton('helper', 'attribute');
        $entities = $attributeHelper->getEntities();

        $current = $page->getParams(['get']);

        foreach ($entities as $entityIdentifier => $options) {
            $allowed = $attributeHelper->getAllowedFilters($entityIdentifier);
            if (empty($allowed)) {
                continue;
            }

            $joins = [];
            $applied = [];

            /** @var Core_Model $model */
            $model = App::getSingleton('model', $entityIdentifier);

            foreach ($current->getData() as $code => $filter) {
                if (!($filter instanceof Leafiny_Object)) {
                    continue;
                }
                if (!isset($allowed[$code])) {
                    continue;
                }
                $alias = 'aev_' . $code;
                if (isset($joins[$alias])) {
                    continue;
                }

                $optionIds = $this->sanitize($filter);
                $allowed[$code]->setData('applied_option_ids', $optionIds);

                $conditions = [
                    'main_table.' . $model->getPrimaryKey() . ' = ' . $alias . '.entity_id',
                    $alias . '.entity_type = "' . $entityIdentifier . '"',
                    $alias . '.language = "' . $page->getLanguage(true) . '"',
                    $alias . '.attribute_code = "' . $code . '"',
                    $alias . '.option_id IN ("' . join('", "', $optionIds) . '")'
                ];
                $joins[$alias] = [
                    'table' => 'attribute_entity_value as ' . $alias,
                    'condition' => join(' AND ', $conditions),
                    'type' => 'INNER',
                ];

                $applied[$code] = $allowed[$code];
            }

            if (!empty($joins)) {
                /** @var Core_Helper $helper */
                $helper = App::getSingleton('helper', $options->getData('helper'));
                $helper->setCustom(
                    'list_joins',
                    array_merge(
                        $joins,
                        $helper->getCustom('list_joins') ?: []
                    )
                );

                $this->updateFilteredPageData($page, $applied);

                App::dispatchEvent(
                    'page_applied_filters',
                    [
                        'page'              => $page,
                        'entity_identifier' => $entityIdentifier,
                        'applied_filters'   => $applied,
                    ]
                );
            }
        }
    }

    /**
     * Update page data
     *
     * @param Core_Page $page
     * @param array $applied
     *
     * @return void
     */
    public function updateFilteredPageData(Core_Page $page, array $applied): void
    {
        /** @var Attribute_Model_Attribute $attributeModel */
        $attributeModel = App::getSingleton('model', 'attribute');

        $page->setCustom('robots', 'NOINDEX,NOFOLLOW');

        $title = $page->getCustom('meta_title') . ' - ' . App::translate('Filtered by:');
        foreach ($applied as $filter) {
            $label = $filter->getData('label');

            try {
                $labels = $attributeModel->getAttributeLabels((int)$filter->getData('attribute_id'));
                foreach ($labels as $local) {
                    if ($local['language'] === $page->getLanguage(true)) {
                        $label = $local['label'];
                    }
                }
            } catch (Throwable $throwable) {
                App::log($throwable, Core_Interface_Log::ERR);
            }

            $title .= ' ' . $label . ',';
        }
        $page->setCustom('meta_title', trim($title, ','));
    }

    /**
     * Retrieve sanitized filter
     *
     * @param Leafiny_Object $filter
     *
     * @return string[]
     */
    protected function sanitize(Leafiny_Object $filter): array
    {
        $data = $filter->getData();
        foreach ($data as $code => $value) {
            if (is_object($value) || is_array($value)) {
                unset($data[$code]);
                continue;
            }
            $data[$code] = addslashes($value);
        }

        return $data;
    }
}
