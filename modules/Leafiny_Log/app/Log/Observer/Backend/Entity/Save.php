<?php

declare(strict_types=1);

/**
 * Class Log_Observer_Backend_Entity_Save
 */
class Log_Observer_Backend_Entity_Save extends Core_Observer_Abstract
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
        /** @var int $objectId */
        $objectId = $object->getData('object_id');
        /** @var string $objectIdentifier */
        $objectIdentifier = $object->getData('identifier');

        if ($objectIdentifier === 'log_db') {
            return;
        }

        /** @var Log_Model_Db $log */
        $log = App::getObject('model', 'log_db');

        $log->add('Object ' . $objectIdentifier . ' with id ' . $objectId . ' has been saved');
    }
}
