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
 * Class Attribute_Observer_Attributes_Save
 */
class Attribute_Observer_Attributes_Save extends Core_Observer implements Core_Interface_Observer
{
    /**
     * Execute
     *
     * @param Leafiny_Object $object
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object $data */
        $data = $object->getData('object');

        if (!$data->getData('_attributes')) {
            return;
        }

        $attributes = $data->getData('_attributes');
        if ($attributes instanceof Leafiny_Object) {
            $attributes = $attributes->getData();
        }

        /** @var Attribute_Model_Attribute $model */
        $model = App::getSingleton('model', 'attribute');

        $model->saveEntityAttributeValues(
            (int)$data->getData($this->getEntityModel()->getPrimaryKey()),
            $data->getData('language'),
            $attributes
        );
    }

    /**
     * Retrieve entity model
     *
     * @return Core_Model
     */
    protected function getEntityModel(): Core_Model
    {
        return App::getSingleton('model', $this->getCustom('entity'));
    }
}
