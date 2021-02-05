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
 * Class Core_Setup
 */
class Core_Setup extends Leafiny_Object
{
    /**
     * Flag for core setup table existence to avoid multiple call on mass upgrade.
     *
     * @var bool|null $coreSetupTableExists
     */
    protected $coreSetupTableExists = null;
    /**
     * Setup table name
     *
     * @var string $setupTable
     */
    protected $setupTable = 'core_setup';

    /**
     * Process Setup
     *
     * @return void
     * @throws Exception
     */
    public function process(): void
    {
        if (!$this->getAdapter()) {
            return;
        }

        if ($this->isLocked()) {
            return;
        }

        $modules = $this->getHelper()->getModules();
        $upgrade = [];

        foreach ($modules as $module) {
            $directory = $this->getHelper()->getSetupDir($module);

            if (!is_dir($directory)) {
                continue;
            }

            $files = scandir($directory, 1);

            foreach ($files as $file) {
                if (!preg_match('/^(?P<sequence>.*)\.upgrade\.sql$/', $file, $matches)) {
                    continue;
                }

                $sequence = (int)$matches['sequence'];
                if (!isset($upgrade[$sequence])) {
                    $upgrade[$sequence] = [];
                }

                $upgrade[$sequence][] = $module . '::' . $file;
            }
        }

        ksort($upgrade);

        foreach ($upgrade as $process) {
            $this->massUpgrade($process);
        }

        $this->lock();
    }

    /**
     * Retrieve Adapter
     *
     * @return MysqliDb|null
     * @throws Exception
     */
    protected function getAdapter(): ?MysqliDb
    {
        /** @var Core_Model $model */
        $model = App::getObject('model');

        return $model->getAdapter();
    }

    /**
     * Check if setup is locked
     *
     * @return bool
     */
    public function isLocked(): bool
    {
        return is_file($this->getLockFile());
    }

    /**
     * Lock setup
     *
     * @return void
     */
    public function lock(): void
    {
        touch($this->getLockFile());
    }

    /**
     * Retrieve lock file
     *
     * @return string
     */
    public function getLockFile(): string
    {
        return $this->getHelper()->getLockDir() . 'setup.lock';
    }

    /**
     * Mass Upgrade files
     *
     * @param string[] $files
     *
     * @return void
     * @throws Exception
     */
    public function massUpgrade(array $files): void
    {
        foreach ($files as $file) {
            $this->upgrade($file);
        }
    }

    /**
     * Upgrade database from SQL file
     *
     * @param string $file
     *
     * @return bool
     * @throws Exception
     */
    public function upgrade(string $file): bool
    {
        if (!$this->tableSetupExists()) {
            $this->createSetupTable();
        }

        if ($this->fileIsProcessed($file)) {
            return false;
        }

        $sql = $this->getModuleFile($file, 'setup');

        if (!is_file($sql)) {
            return false;
        }

        if (!file_get_contents($sql)) {
            return false;
        }

        if (!$this->processFile($sql)) {
            return false;
        }

        $this->getAdapter()->insert($this->setupTable, ['file' => $file]);

        return true;
    }

    /**
     * Check if file is already processed
     *
     * @param string $file
     *
     * @return bool
     * @throws Exception
     */
    public function fileIsProcessed(string $file): bool
    {
        $adapter = $this->getAdapter();
        $adapter->where('file', $file);

        return (bool)$adapter->getOne($this->setupTable);
    }

    /**
     * Check if setup table exists
     *
     * @return bool
     * @throws Exception
     */
    public function tableSetupExists(): bool
    {
        if ($this->coreSetupTableExists !== null) {
            return $this->coreSetupTableExists;
        }

        $this->coreSetupTableExists = (bool)$this->getAdapter()->tableExists([$this->setupTable]);

        return $this->coreSetupTableExists;
    }

    /**
     * Create setup table
     *
     * @throws Exception
     */
    public function createSetupTable(): void
    {
        $this->getAdapter()->query("
            CREATE TABLE IF NOT EXISTS `" . $this->setupTable . "` (
                file VARCHAR(255) NOT NULL,
                UNIQUE KEY (file)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8
        ");
    }

    /**
     * Insert all queries in sql file
     *
     * @param string $file
     *
     * @return bool
     * @throws Exception
     */
    public function processFile(string $file): bool
    {
        if (!is_file($file)) {
            return false;
        }

        $fp = fopen($file, 'r');
        $query = '';

        $adapter = $this->getAdapter();

        $adapter->startTransaction();

        while (($line = fgets($fp)) !== false) {
            if ($line === '') {
                continue;
            }
            if (substr($line, 0, 2) === '--') {
                continue;
            }
            if (substr($line, 0, 1) === '#') {
                continue;
            }

            $query .= $line;

            if (substr(trim($line), -1, 1) === ';') {
                $adapter->query($query);
                if ($adapter->getLastErrno()) {
                    $adapter->rollback();
                    throw new Exception($adapter->getLastError());
                }
                $query = '';
            }
        }

        $adapter->commit();

        fclose($fp);

        return true;
    }

    /**
     * Retrieve File
     *
     * @param string $path
     * @param string $type
     *
     * @return string
     */
    public function getModuleFile(string $path, string $type): string
    {
        $helper = $this->getHelper();

        return $helper->getModulesDir() . $helper->getModuleFile($path, $type);
    }

    /**
     * Retrieve Helper
     *
     * @return Core_Helper
     */
    public function getHelper(): Core_Helper
    {
        return App::getSingleton('helper');
    }
}
