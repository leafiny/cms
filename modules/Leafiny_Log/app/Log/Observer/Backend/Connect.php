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
 * Class Log_Observer_Backend_Connect
 */
class Log_Observer_Backend_Connect extends Core_Event implements Core_Interface_Event
{
    /**
     * Execute
     *
     * @param Core_Page|Leafiny_Object $object
     *
     * @return void
     * @throws Exception
     */
    public function execute(Leafiny_Object $object): void
    {
        /** @var Leafiny_Object $user */
        $user = $object->getData('user');

        $fullName = $user->getData('firstname') . ' ' . $user->getData('lastname');

        Log::db('User ' . $fullName . ' is connected to backend');
    }
}
