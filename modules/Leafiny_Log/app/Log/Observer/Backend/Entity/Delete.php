<?php

declare(strict_types=1);

/**
 * Class Log_Observer_Backend_Entity_Delete
 */
class Log_Observer_Backend_Entity_Delete extends Core_Event
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
        /** @var int[] $objectIds */
        $objectIds = $object->getData('object_ids');
        /** @var string $objectIdentifier */
        $objectIdentifier = $object->getData('identifier');

        if ($objectIdentifier === 'log_db') {
            return;
        }

        /** @var Log_Model_Db $log */
        $log = App::getObject('model', 'log_db');

        $count = count($objectIds);

        if ($count === 1) {
            $log->add(
                'Object ' . $objectIdentifier . ' with id ' . join(',', $objectIds) . ' has been deleted'
            );
        }

        if ($count > 1) {
            $log->add(
                'Objects ' . $objectIdentifier . ' with ids ' . join(',', $objectIds) . ' have been deleted'
            );
        }
    }
}
