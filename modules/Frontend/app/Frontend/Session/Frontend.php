<?php

declare(strict_types=1);

/**
 * Class Frontend_Session_Frontend
 */
class Frontend_Session_Frontend extends Core_Session
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
        parent::init($context ?: Core_Template_Abstract::CONTEXT_DEFAULT);
    }
}
