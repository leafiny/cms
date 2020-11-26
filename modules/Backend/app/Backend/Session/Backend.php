<?php

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
