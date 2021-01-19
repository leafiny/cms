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
 * Class Log
 */
final class Log
{
    /**
     * Log data in database
     *
     * @param mixed $data
     * @param int $level
     *
     * @return void
     * @throws Exception
     */
    public static function db($data, int $level = Core_Interface_Log::INFO): void
    {
        /** @var Log_Model_Db $log */
        $log = App::getSingleton('model', 'log_db');
        $log->add($data, $level);
    }

    /**
     * Log data in file
     *
     * @param mixed $data
     * @param int $level
     *
     * @return void
     */
    public static function file($data, int $level = Core_Interface_Log::INFO): void
    {
        /** @var Log_Model_File $log */
        $log = App::getSingleton('model', 'log_file');
        $log->add($data, $level);
    }

    /**
     * Default save log method
     *
     * @param mixed $data
     * @param int $level
     *
     * @return void
     */
    public static function save($data, int $level = Core_Interface_Log::INFO): void
    {
        self::file($data, $level);
    }
}
