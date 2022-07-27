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

class Attribute_Observer_Attributes_Get extends Core_Observer implements Core_Interface_Observer
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
        /** @var Leafiny_Object $data */
        $data = $object->getData('object');
        if (!$data->hasData()) {
            return;
        }

        /** @var Attribute_Model_Attribute $model */
        $model = App::getSingleton('model', 'attribute');

        try {
            $data->setData(
                'attributes',
                new Leafiny_Object(
                    $model->getEntityAttributeValues(
                        (int)$data->getData($this->getEntityModel()->getPrimaryKey()),
                        $this->getEntityModel()->getObjectIdentifier(),
                        $data->getData('language')
                    )
                )
            );
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }
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
