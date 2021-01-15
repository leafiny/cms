<?php

declare(strict_types=1);

/**
 * Class Log_Observer_Backend_Cache_Flush_Error
 */
class Log_Observer_Backend_Cache_Flush_Error extends Core_Event implements Core_Interface_Event
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
        /** @var string $type */
        $type = $object->getData('type');
        /** @var string $type */
        $error = $object->getData('error');

        Log::db('Error deleting cache for ' . $type . ': ' . $error, Core_Interface_Log::ERR);
    }
}
