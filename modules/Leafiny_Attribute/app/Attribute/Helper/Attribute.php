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
 * Class Attribute_Helper_Attribute
 */
class Attribute_Helper_Attribute extends Core_Helper
{
    /**
     * Retrieve all attributes with values for the specified object
     *
     * @param int         $objectId
     * @param string      $entityType
     * @param string|null $language
     *
     * @return Leafiny_Object
     */
    public function getAttributes(int $objectId, string $entityType, string $language = null): Leafiny_Object
    {
        if ($language === null) {
            $language = App::getLanguage();
        }

        /** @var Attribute_Model_Attribute $attributeModel */
        $attributeModel = App::getSingleton('model', 'attribute');

        try {
            return new Leafiny_Object($attributeModel->getEntityAttributeValues($objectId, $entityType, $language));
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return new Leafiny_Object();
    }

    /**
     * Retrieve all entities with attribute system
     *
     * @return Leafiny_Object[]
     */
    public function getEntities(): array
    {
        $entities = $this->getCustom('entity');
        if (!$entities) {
            return [];
        }

        $result = [];
        foreach ($entities as $identifier => $options) {
            if (!($options['enabled'] ?? false)) {
                continue;
            }
            $result[$identifier] = new Leafiny_Object($options ?? []);
        }

        return $result;
    }

    /**
     * Retrieve allowed filter codes for entity
     *
     * @param string $entityType
     *
     * @return string[]
     */
    public function getAllowedFilters(string $entityType): array
    {
        /** @var Attribute_Model_Attribute $attributeModel */
        $attributeModel = App::getSingleton('model', 'attribute');

        $filters = [
            [
                'column' => 'entity_type',
                'value'  => $entityType
            ],
            [
                'column' => 'is_filterable',
                'value'  => 1
            ],
        ];

        $allowed = [];

        try {
            $attributes = $attributeModel->getList($filters);
            foreach ($attributes as $attribute) {
                $allowed[] = $attribute->getData('code');
            }
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return $allowed;
    }

    /**
     * Retrieve input types
     *
     * @return string[]
     */
    public function getInputTypes(): array
    {
        if ($this->getCustom('input_types')) {
            return array_filter($this->getCustom('input_types'));
        }

        return [
            'text'        => 'Text',
            'select'      => 'Select',
            'multiselect' => 'Multiselect',
        ];
    }

    /**
     * Input type with options
     *
     * @return string[]
     */
    public function getInputTypesWithOptions(): array
    {
        if ($this->getCustom('input_types_with_options')) {
            return array_filter($this->getCustom('input_types_with_options'));
        }

        return ['select', 'multiselect'];
    }
}
