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
 * Interface Core_Interface_Event
 * @deprecated since 1.8.1
 * @see Core_Interface_Observer
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
