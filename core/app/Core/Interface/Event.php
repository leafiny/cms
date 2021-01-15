<?php

declare(strict_types=1);

/**
 * Interface Core_Interface_Event
 */
interface Core_Interface_Event
{
    /**
     * Add Log
     *
     * @param Leafiny_Object $object
     *
     * @return void
     */
    public function execute(Leafiny_Object $object): void;
}
