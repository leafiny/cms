<?php

declare(strict_types=1);

/**
 * Class Core_Config
 */
class Core_Config extends Leafiny_Config
{
    /**
     * @var string CACHE_CONFIG_DIRECTORY
     */
    public const CACHE_CONFIG_DIRECTORY = 'config';

    /**
     * Core_Config constructor.
     *
     * @param bool $flat
     * @throws Exception
     */
    public function __construct(bool $flat = true)
    {
        $this->loadConfig($flat);
    }

    /**
     * Load config
     *
     * @param bool $flat
     *
     * @return Core_Config
     * @throws Exception
     */
    public function loadConfig(bool $flat = true): Core_Config
    {
        $cache = $this->getCache();

        if ($cache) {
            $config = $cache->retrieve($this->getCacheKey($flat));

            if ($config) {
                $this->result = $config;
                return $this;
            }
        }

        $files = [
            $this->getHelper()->getEnvironmentConfigFile()
        ];

        $modules = $this->getHelper()->getModules();
        foreach ($modules as $module) {
            $file = $this->getHelper()->getModelConfigFile($module);
            if (is_file($file)) {
                $files[] = $file;
            }
        }

        $this->setFiles($files);
        $this->load($flat);

        if ($cache) {
            $cache->store($this->getCacheKey($flat), $this->result);
        }

        if ($this->get('app.config_cache')) {
            $this->lock();
        }

        return $this;
    }

    /**
     * Retrieve cache
     *
     * @return Leafiny_Cache|null
     */
    public function getCache(): ?Leafiny_Cache
    {
        if (!$this->canCache()) {
            return null;
        }

        $cache = new Leafiny_Cache();
        $cache->setCachePath(
            $this->getHelper()->getCacheDir() . self::CACHE_CONFIG_DIRECTORY . DS
        );
        $cache->setCacheName(App::getEnvironment() . '_' . App::getLanguage());

        return $cache;
    }

    /**
     * Retrieve cache key
     *
     * @param bool $flat
     *
     * @return string
     */
    public function getCacheKey(bool $flat = true): string
    {
        return $flat ? 'flat' : 'full';
    }

    /**
     * Retrieve if config can be cached
     *
     * @return bool
     */
    public function canCache(): bool
    {
        if (is_file($this->getLockFile())) {
            return true;
        }

        return false;
    }

    /**
     * Lock config
     *
     * @return void
     */
    public function lock(): void
    {
        if (!is_file($this->getLockFile())) {
            touch($this->getLockFile());
        }
    }

    /**
     * Unlock config
     *
     * @return void
     */
    public function unlock(): void
    {
        if (is_file($this->getLockFile())) {
            unlink($this->getLockFile());
        }
    }

    /**
     * Retrieve Lock File
     *
     * @return string
     */
    public function getLockFile(): string
    {
        return $this->getHelper()->getLockDir() . 'config.lock';
    }

    /**
     * Retrieve Helper
     *
     * @return Core_Helper
     */
    public function getHelper(): Core_Helper
    {
        return new Core_Helper;
    }
}
