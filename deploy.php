<?php

declare(strict_types=1);

define('DS', DIRECTORY_SEPARATOR);

$mediaDir   = __DIR__ . DS . 'media'  . DS;
$pubDir     = __DIR__ . DS . 'pub'  . DS;
$modulesDir = __DIR__ . DS . 'modules'  . DS;
$varDir     = __DIR__ . DS . 'var'  . DS;

$cryptKeyDir = $varDir . 'crypt' . DS;

if (!is_dir($cryptKeyDir)) {
    mkdir($cryptKeyDir);
}

$cryptKeyFile = $cryptKeyDir . 'salt.key';

if (!is_file($cryptKeyFile)) {
    $salt =  uniqid('', true) . '.' . rand(10000000, 99999999);
    file_put_contents($cryptKeyFile, $salt);
}

$salt = file_get_contents($cryptKeyFile);

if (!is_link($pubDir . 'media')) {
    symlink($mediaDir, $pubDir . 'media');
}

$modules = scandir($modulesDir, 1);

$links = ['skin', 'media'];

foreach ($modules as $module) {
    if ($module === '..') {
        continue;
    }
    foreach ($links as $directory) {
        $target = $modulesDir . $module . DS . $directory;
        $link   = $pubDir . 'modules' . DS . md5($salt . $module) . DS . $directory;

        if (is_dir($target) && !is_dir(dirname($link))) {
            mkdir(dirname($link), 0777, true);
        }

        if (is_dir($target) && !is_link($link)) {
            symlink($target, $link);
        }
    }
}
