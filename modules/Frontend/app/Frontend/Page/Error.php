<?php

/**
 * Class Frontend_Page_Error
 */
class Frontend_Page_Error extends Core_Page
{
    /**
     * Execute action
     *
     * @return void
     */
    public function action(): void
    {
        parent::action();

        header('HTTP/1.0 404 Not Found');
    }
}
