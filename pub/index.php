<?php

ini_set('display_errors', '0');

if (isset($_SERVER['DEVELOPER_MODE'])) {
    error_reporting(E_ALL | E_STRICT);
    ini_set('display_errors', '1');
}

require_once '../core/App.php';

App::setRootDir(App::getRootDir() . '..' . DS);

App::run();
