<?php

declare(strict_types=1);

/**
 * Class Log_Observer_Backend_Connect
 */
class Log_Observer_Backend_Connect extends Core_Event
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

        /** @var Log_Model_Db $log */
        $log = App::getObject('model', 'log_db');

        $fullName = $user->getData('firstname') . ' ' . $user->getData('lastname');

        $log->add('User ' . $fullName . ' is connected to backend');
    }
}
