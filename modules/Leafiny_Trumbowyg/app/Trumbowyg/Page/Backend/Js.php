<?php

declare(strict_types=1);

/**
 * Class Trumbowyg_Page_Backend_Js
 */
class Trumbowyg_Page_Backend_Js extends Backend_Page_Admin_Page_Abstract
{
    /**
     * Execute action
     *
     * @return void
     * @throws Exception
     */
    public function action(): void
    {
        parent::action();

        header('Content-Type: application/javascript');
        header("Cache-Control: max-age=604800, public");

        header_remove('pragma');
    }
}
