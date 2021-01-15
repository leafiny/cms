<?php

declare(strict_types=1);

/**
 * Class Log_Model_Db
 */
class Log_Model_Db extends Core_Model implements Core_Interface_Log
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'log_message';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'log_id';

    /**
     * Add Log
     *
     * @param mixed $message
     * @param int   $level
     *
     * @return int
     * @throws Exception
     */
    public function add($message, int $level = self::INFO): int
    {
        if ($message instanceof Throwable) {
            $message = $message->getMessage() . "\n" . $message->getTraceAsString();
        }
        if (is_array($message) || is_object($message)) {
            $message = print_r($message, true);
        }
        $message = addcslashes($message, '<?');

        $data = new Leafiny_Object(
            [
                'level'   => $level,
                'message' => $message,
            ]
        );

        return $this->save($data);
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
