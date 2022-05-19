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
 * Class Attribute_Model_Attribute
 */
class Attribute_Model_Attribute extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'attribute';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'attribute_id';

    /**
     * Save the attribute
     *
     * @param Leafiny_Object $object
     *
     * @return int|null
     * @throws Exception
     */
    public function save(Leafiny_Object $object): ?int
    {
        $labels = $object->getData('label');
        if ($labels instanceof Leafiny_Object) {
            $labels = $labels->getData();
        }
        if (is_array($labels)) {
            if (!isset($labels[App::getLanguage()])) {
                throw new Exception('The label for ' . App::getLanguage() . ' is missing');
            }
            $object->setData('label', (string)$labels[App::getLanguage()]);
        }

        if ($object->getData('code')) {
            /** @var Core_Helper $coreHelper */
            $coreHelper = App::getObject('helper');
            $object->setData('code', $coreHelper->formatKey($object->getData('code'), [], [], '_'));
        }

        /** @var Attribute_Helper_Attribute $attributeHelper */
        $attributeHelper = App::getSingleton('helper', 'attribute');

        if (!in_array($object->getData('input_type'), $attributeHelper->getInputTypesWithOptions())) {
            $object->setData('option_qty', 0);
        }

        $attributeId = parent::save($object);

        if (is_array($labels)) {
            $this->saveAttributeLabels($attributeId, $labels);
        }

        if (!in_array($object->getData('input_type'), $attributeHelper->getInputTypesWithOptions())) {
            $object->setData('options', null);
            $this->removeOptions($attributeId);
        }

        if ($object->getData('options')) {
            $options = $object->getData('options');
            if ($object->getData('options') instanceof Leafiny_Object) {
                $options = $options->getData();
            }
            $this->addOptions($attributeId, $options);
        }

        return $attributeId;
    }

    /**
     * Retrieve attribute
     *
     * @param mixed $value
     * @param string|null $column
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function get($value, ?string $column = null): Leafiny_Object
    {
        $attribute = parent::get($value, $column);

        $attribute->setData('label', [App::getLanguage() => $attribute->getData('label')]);
        $labels = $this->getAttributeLabels((int)$attribute->getData('attribute_id'));
        foreach ($labels as $label) {
            $attribute->setData(
                'label',
                array_merge(
                    $attribute->getData('label'),
                    [
                        $label['language'] => $label['label']
                    ]
                )
            );
        }

        $attribute->setData('options', $this->getOptions((int)$attribute->getData('attribute_id')));

        return $attribute;
    }

    /**
     * Retrieve attribute labels
     *
     * @param int $attributeId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getAttributeLabels(int $attributeId): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('attribute_id', $attributeId);

        return $adapter->get('attribute_translate');
    }

    /**
     * Save attribute labels
     *
     * @param int      $attributeId
     * @param string[] $labels
     *
     * @return bool
     * @throws Exception
     */
    public function saveAttributeLabels(int $attributeId, array $labels): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        foreach ($labels as $language => $label) {
            $adapter->insert(
                'attribute_translate',
                [
                    'attribute_id' => $attributeId,
                    'label'        => $label,
                    'language'     => $language,
                ]
            );
            if ($adapter->getLastErrno()) {
                throw new Exception($this->getAdapter()->getLastError());
            }
        }

        return true;
    }

    /**
     * Retrieve options for the attribute
     *
     * @param int $attributeId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getOptions(int $attributeId): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('attribute_id', $attributeId);
        $adapter->orderBy('position', 'ASC');
        $result = $adapter->get('attribute_option', null, ['option_id']);

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        $options = [];
        foreach ($result as $option) {
            $options[] = $this->getOption((int)$option['option_id']);
        }

        return $options;
    }

    /**
     * Retrieve the option
     *
     * @param int $optionId
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getOption(int $optionId): Leafiny_Object
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return new Leafiny_Object();
        }

        $adapter->where('option_id', $optionId);
        $option = $adapter->getOne('attribute_option');

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        if (!$option) {
            return new Leafiny_Object();
        }

        $object = new Leafiny_Object();
        $object->setData($option);

        $object->setData('label', [App::getLanguage() => $object->getData('label')]);

        $adapter->where('option_id', $object->getData('option_id'));
        $labels = $adapter->get('attribute_option_translate');
        foreach ($labels as $label) {
            $object->setData(
                'label',
                array_merge(
                    $object->getData('label'),
                    [
                        $label['language'] => $label['label']
                    ]
                )
            );
        }

        return $object;
    }

    /**
     * Add options for the attribute
     *
     * @param int $attributeId
     * @param array $options
     *
     * @return bool
     * @throws Exception
     */
    public function addOptions(int $attributeId, array $options): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        foreach ($options as $option) {
            if (!isset($option['label'][App::getLanguage()])) {
                continue;
            }
            if (!$option['label'][App::getLanguage()]) {
                continue;
            }

            $custom = $option['custom'] ?? null;
            if (!$custom) {
                $custom = null;
            }

            $data = [
                'attribute_id' => $attributeId,
                'label'        => $option['label'][App::getLanguage()],
                'custom'       => $custom,
                'position'     => (int)($option['position'] ?? 0),
            ];
            if ($option['option_id'] ?? false) {
                $data['option_id'] = $option['option_id'];
            }

            $adapter->onDuplicate(['label', 'custom', 'position']);
            $adapter->insert('attribute_option', $data);

            if ($adapter->getLastErrno()) {
                throw new Exception($this->getAdapter()->getLastError());
            }

            $optionId = $adapter->getInsertId();
            if (!$optionId) {
                continue;
            }
            $adapter->where('option_id', $optionId);
            $adapter->delete('attribute_option_translate');
            foreach ($option['label'] as $language => $label) {
                $adapter->insert(
                    'attribute_option_translate',
                    [
                        'option_id' => $optionId,
                        'label'     => $label,
                        'language'  => $language,
                    ]
                );
                if ($adapter->getLastErrno()) {
                    throw new Exception($this->getAdapter()->getLastError());
                }
            }
        }

        return true;
    }

    /**
     * Remove the option
     *
     * @param int $optionId
     *
     * @return bool
     * @throws Exception
     */
    public function removeOption(int $optionId): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('option_id', $optionId);
        $adapter->delete('attribute_option');

        $adapter->where('option_id', $optionId);
        $adapter->delete('attribute_entity_value');

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        return true;
    }

    /**
     * Remove all the options
     *
     * @param int $attributeId
     *
     * @return bool
     * @throws Exception
     */
    public function removeOptions(int $attributeId): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('attribute_id', $attributeId);
        $options = $adapter->get('attribute_option');

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        foreach ($options as $option) {
            $this->removeOption((int)$option['option_id']);
        }

        return true;
    }

    /**
     * Retrieve entity attribute values
     *
     * @param int    $entityId
     * @param string $entityType
     * @param string $language
     *
     * @return mixed[]
     * @throws Exception
     */
    public function getEntityAttributeValues(int $entityId, string $entityType, string $language): array
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return [];
        }

        $adapter->where('language', $language);
        $adapter->where('entity_id', $entityId);
        $adapter->where('entity_type', $entityType);

        $result = $adapter->get('attribute_entity_value');

        $values = [];
        foreach ($result as $row) {
            if (!isset($values[$row['attribute_code']])) {
                $values[$row['attribute_code']] = [];
            }
            $values[$row['attribute_code']][$row['option_id'] ?: 0] = new Leafiny_Object(
                [
                    'value'  => $row['value'],
                    'custom' => $row['option_custom'] ?: null,
                ]
            );
        }

        return $values;
    }

    /**
     * Save entity attribute values
     *
     * @param int    $entityId
     * @param string $language
     * @param array  $attributes
     *
     * @return bool
     * @throws Exception
     */
    public function saveEntityAttributeValues(int $entityId, string $language, array $attributes): bool
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        /** @var Attribute_Helper_Attribute $helper */
        $helper = App::getSingleton('helper', 'attribute');

        foreach ($attributes as $code => $values) {
            $attribute = $this->get($code, 'code');
            if (!$attribute->getData('attribute_id')) {
                continue;
            }

            $adapter->where('attribute_id', $attribute->getData('attribute_id'));
            $adapter->where('language', $language);
            $adapter->where('entity_id', $entityId);
            $adapter->delete('attribute_entity_value');

            foreach ($values as $value) {
                $optionId = 0;
                $optionCustom = null;

                if (in_array($attribute->getData('input_type'), $helper->getInputTypesWithOptions())) {
                    $option = $this->getOption((int)$value);
                    if (!$option->getData('option_id')) {
                        continue;
                    }
                    $optionId     = $option->getData('option_id');
                    $optionCustom = $option->getData('custom');
                    $value        = $option->getData('label')[$language];
                }

                $data = [
                    'attribute_id' => $attribute->getData('attribute_id'),
                    'attribute_code' => $attribute->getData('code'),
                    'option_id' => $optionId,
                    'option_custom' => $optionCustom,
                    'language' => $language,
                    'entity_type' => $attribute->getData('entity_type'),
                    'entity_id' => $entityId,
                    'value' => $value,
                ];

                $adapter->onDuplicate(['option_custom', 'value']);
                $adapter->insert('attribute_entity_value', $data);

                if ($adapter->getLastErrno()) {
                    throw new Exception($this->getAdapter()->getLastError());
                }
            };
        };

        return true;
    }

    /*
     * Validation
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

        if (!$object->getData('input_type')) {
            return 'The input type cannot be empty';
        }
        if (!$object->getData('code')) {
            return 'The code cannot be empty';
        }
        if (!$object->getData('label')) {
            return 'The label cannot be empty';
        }

        return '';
    }
}
