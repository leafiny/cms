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
 * Class Log_Model_File
 */
class Log_Model_File extends Leafiny_Object implements Core_Interface_Log
{
    /**
     * @var string LOG_DIR
     */
    public const LOG_DIR = 'log';

    /**
     * @var string DEFAULT_LOG_FILE
     */
    public const DEFAULT_LOG_FILE = 'system.log';

    /**
     * Log File
     *
     * @var string $logFile
     */
    protected $logFile = self::DEFAULT_LOG_FILE;

    /**
     * Add Log
     *
     * @param mixed  $message
     * @param int    $level
     * @param string $logFile
     *
     * @return int
     */
    public function add($message, int $level = self::INFO, ?string $logFile = null): int
    {
        if ($logFile) {
            $this->setLogFile($logFile);
        }
        if ($message instanceof Throwable) {
            $message = $message->getMessage() . "\n" . $message->getTraceAsString();
        }
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        $message = addcslashes((string)$message, '<?');
        $message = $this->getPrefix($level) . $message . "\n";

        /** @var Core_Helper $helper */
        $helper = App::getObject('helper');

        $logDirectory = $helper->getVarDir() . self::LOG_DIR . DS;

        if (!is_dir($logDirectory)) {
            mkdir($logDirectory);
        }

        $logFile = $logDirectory . $this->getLogFile();

        return (int)file_put_contents($logFile, $message, FILE_APPEND);
    }

    /**
     * Retrieve Log File
     *
     * @return string
     */
    public function getLogFile(): string
    {
        return $this->logFile;
    }

    /**
     * Set log file
     *
     * @param string $logFile
     */
    public function setLogFile(string $logFile): void
    {
        $this->logFile = $logFile;
    }

    /**
     * Retrieve message prefix
     *
     * @param int $level
     *
     * @return string
     */
    public function getPrefix(int $level): string
    {
        $data = [
            date('Y-m-d H:i:s'),
            str_pad(strtoupper($this->getLevelText($level)), 10),
            ''
        ];

        return join(' | ', $data);
    }

    /**
     * Retrieve level text
     *
     * @param int $level
     *
     * @return string
     */
    public function getLevelText(int $level): string
    {
        $levels = $this->getLevels();

        return isset($levels[$level]) ? $levels[$level] : '';
    }

    /**
     * Retrieve levels
     *
     * @return string[]
     */
    public function getLevels(): array
    {
        return [
            self::EMERG  => 'Emergency',
            self::ALERT  => 'Alert',
            self::CRIT   => 'Critical',
            self::ERR    => 'Error',
            self::WARN   => 'Warning',
            self::NOTICE => 'Notice',
            self::INFO   => 'Info',
            self::DEBUG  => 'Debug'
        ];
    }
}
