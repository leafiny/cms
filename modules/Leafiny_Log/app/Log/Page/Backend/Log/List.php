<?php

declare(strict_types=1);

/**
 * Class Log_Page_Backend_Log_List
 */
class Log_Page_Backend_Log_List extends Backend_Page_Admin_List
{
    /**
     * Retrieve levels list
     *
     * @return string[]
     */
    public function getLevels(): array
    {
        /** @var Log_Model_Db $log */
        $log = $this->getModel();

        return array_map('strtoupper', $log->getLevels());
    }

    /**
     * Retrieve level as text
     *
     * @param int $level
     *
     * @return string
     */
    public function getLevelText(int $level): string
    {
        /** @var Log_Model_Db $log */
        $log = $this->getModel();

        return strtoupper($log->getLevelText($level));
    }
}
