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
 * Class Backend_Observer_User_SaveResources
 */
class Backend_Observer_User_SaveResources extends Core_Event implements Core_Interface_Event
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
        /** @var string $identifier */
        $identifier = $object->getData('identifier');
        if ($identifier !== 'admin_user') {
            return;
        }

        /** @var int $userId */
        $userId = $object->getData('object_id');
        if (!$userId) {
            return;
        }

        /** @var Leafiny_Object $data */
        $data = $object->getData('data');
        /** @var Leafiny_Object|null $resources */
        $resources = $data->getData('resources');

        if (!$resources) {
            return;
        }

        if (!$resources instanceof Leafiny_Object) {
            return;
        }

        /** @var Backend_Model_Admin_User_Resources $model */
        $model = App::getObject('model', 'admin_user_resources');

        $model->update($userId, $resources->getData());
    }
}
