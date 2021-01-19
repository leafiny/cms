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
 * Class Backend_Session_Backend
 */
class Backend_Session_Backend extends Core_Session
{
    /**
     * Init Session
     *
     * @param string $context
     *
     * @return void
     * @throws Exception
     */
    public function init(string $context = ''): void
    {
        parent::init($context ?: Backend_Page_Admin_Page_Abstract::CONTEXT_BACKEND);
    }
}
