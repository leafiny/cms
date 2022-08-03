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

class Attribute_Block_Backend_Form_Attributes extends Core_Block
{
    /**
     * The attribute list
     *
     * @var Leafiny_Object[]|null
     */
    protected $attributes = null;

    /**
     * Can show attributes
     *
     * @return bool
     * @throws Exception
     */
    public function canShow(): bool
    {
        /** @var Attribute_Helper_Attribute $helper */
        $helper = $this->getHelper('attribute');

        return in_array($this->getEntity(), array_keys($helper->getEntities())) && count($this->getAttributes());
    }

    /**
     * Retrieve attributes
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getAttributes(): array
    {
        if ($this->attributes !== null) {
            return $this->attributes;
        }

        /** @var Attribute_Model_Attribute $model */
        $model = App::getSingleton('model', 'attribute');

        $filters = [
            [
                'column' => 'entity_type',
                'value'  => $this->getEntity(),
            ]
        ];

        $orders = [
            [
                'order' => 'position',
                'dir'   => 'ASC'
            ],
        ];

        $this->attributes = $model->getList($filters, $orders);

        return $this->attributes;
    }

    /**
     * Retrieve options
     *
     * @param int $attributeId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getOptions(int $attributeId): array
    {
        /** @var Attribute_Model_Attribute $model */
        $model = App::getSingleton('model', 'attribute');

        return $model->getOptions($attributeId);
    }

    /**
     * Retrieve entity
     *
     * @return string
     */
    public function getEntity(): string
    {
        return $this->getCustom('entity') ?: 'default';
    }
}
