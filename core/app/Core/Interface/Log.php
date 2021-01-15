<?php

declare(strict_types=1);

/**
 * Interface Core_Interface_Log
 */
interface Core_Interface_Log
{
    public const EMERG  = 0;  // Emergency: system is unusable
    public const ALERT  = 1;  // Alert: action must be taken immediately
    public const CRIT   = 2;  // Critical: critical conditions
    public const ERR    = 3;  // Error: error conditions
    public const WARN   = 4;  // Warning: warning conditions
    public const NOTICE = 5;  // Notice: normal but significant condition
    public const INFO   = 6;  // Informational: informational messages
    public const DEBUG  = 7;  // Debug: debug messages

    /**
     * Add Log
     *
     * @param mixed $message
     * @param int   $level
     *
     * @return int
     */
    public function add($message, int $level = self::INFO): int;
}
