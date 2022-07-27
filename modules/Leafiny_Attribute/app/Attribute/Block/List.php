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

class Attribute_Block_List extends Core_Block
{
    /**
     * Retrieve all attributes for given entity
     *
     * @param Leafiny_Object|null $entity
     *
     * @return Leafiny_Object[]
     */
    public function getAttributes(?Leafiny_Object $entity): array
    {
        if (!$entity) {
            return [];
        }

        /** @var Leafiny_Object|null $attributes */
        $attributes = $entity->getData('attributes');

        if (!$entity->getData('attributes')) {
            return [];
        }

        $allowed = $this->getShowInListAttributes();

        foreach ($attributes->getData() as $code => $option) {
            if (in_array($code, $allowed)) {
                continue;
            }
            unset($attributes[$code]);
        }

        return $attributes->getData();
    }

    /**
     * Retrieve attributes to show in list
     *
     * @return array
     */
    public function getShowInListAttributes(): array
    {
        /** @var Attribute_Model_Attribute $attributeModel */
        $attributeModel = App::getSingleton('model', 'attribute');

        try {
            return $attributeModel->getShowInListAttributeCodes();
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return [];
    }
}
