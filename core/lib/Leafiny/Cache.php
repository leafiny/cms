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
 * Class Leafiny_Cache
 */
class Leafiny_Cache
{
    /**
     * The path to the cache file folder
     *
     * @var string $cachePath
     */
    private $cachePath = 'cache';

    /**
     * The name of the default cache file
     *
     * @var string $cacheName
     */
    private $cacheName = 'default';

    /**
     * The cache file extension
     *
     * @var string $extension
     */
    private $extension = '.cache';

    /**
     * Check whether data associated with a key
     *
     * @param string $key
     *
     * @return boolean
     * @throws Exception
     */
    public function isCached(string $key): bool
    {
        $cachedData = $this->loadCache();

        if (empty($cachedData)) {
            return false;
        }

        return isset($cachedData[$key]['data']);
    }

    /**
     * Store data in the cache
     *
     * @param string $key
     * @param mixed $data
     * @param int $expiration
     *
     * @return Leafiny_Cache
     * @throws Exception
     */
    public function store(string $key, $data, int $expiration = 0): Leafiny_Cache
    {
        $storeData = [
            'time'   => time(),
            'expire' => $expiration,
            'data'   => serialize($data)
        ];

        $data = $this->loadCache();
        $data[$key] = $storeData;

        $cacheData = json_encode($data);

        file_put_contents($this->getCacheDir(), $cacheData);

        return $this;
    }

    /**
     * Retrieve cached data by its key
     *
     * @param string $key
     * @param bool $timestamp
     *
     * @return mixed
     * @throws Exception
     */
    public function retrieve(string $key, bool $timestamp = false)
    {
        $cachedData = $this->loadCache();

        $type = 'time';
        if ($timestamp === false) {
            $type = 'data';
        }

        if (!isset($cachedData[$key][$type])) {
            return null;
        }

        return unserialize($cachedData[$key][$type]);
    }

    /**
     * Retrieve all cached data
     *
     * @param boolean $meta
     *
     * @return array
     * @throws Exception
     */
    public function retrieveAll(bool $meta = false): array
    {
        if ($meta === false) {
            $results = [];
            $cachedData = $this->loadCache();

            if (empty($cachedData)) {
                return $results;
            }

            foreach ($cachedData as $key => $value) {
                $results[$key] = unserialize($value['data']);
            }

            return $results;
        }

        return $this->loadCache();
    }

    /**
     * Erase cached entry by its key
     *
     * @param string $key
     *
     * @return Leafiny_Cache
     * @throws Exception
     */
    public function erase(string $key): Leafiny_Cache
    {
        $cacheData = $this->loadCache();

        if (empty($cacheData)) {
            return $this;
        }

        unset($cacheData[$key]);

        $cacheData = json_encode($cacheData);

        file_put_contents($this->getCacheDir(), $cacheData);

        return $this;
    }

    /**
     * Erase all expired entries
     *
     * @return int
     * @throws Exception
     */
    public function eraseExpired(): int
    {
        $cacheData = $this->loadCache();
        $counter   = 0;

        if (empty($cacheData)) {
            return $counter;
        }

        foreach ($cacheData as $key => $entry) {
            if (!$this->checkExpired($entry['time'], $entry['expire'])) {
                continue;
            }

            unset($cacheData[$key]);

            $counter++;
        }

        if ($counter > 0) {
            $cacheData = json_encode($cacheData);

            file_put_contents($this->getCacheDir(), $cacheData);
        }

        return $counter;
    }

    /**
     * Erase all cached entries
     *
     * @return Leafiny_Cache
     * @throws Exception
     */
    public function eraseAll(): Leafiny_Cache
    {
        $cacheDir = $this->getCacheDir();

        if (file_exists($cacheDir)) {
            $cacheFile = fopen($cacheDir, 'w');
            fclose($cacheFile);
        }

        return $this;
    }

    /**
     * Load appointed cache
     *
     * @return array
     * @throws Exception
     */
    private function loadCache(): array
    {
        $cacheDir = $this->getCacheDir();

        if (!file_exists($cacheDir)) {
            return [];
        }

        $file = file_get_contents($cacheDir);

        return json_decode($file, true) ?: [];
    }

    /**
     * Get the cache directory path
     *
     * @return string
     * @throws Exception
     */
    public function getCacheDir(): string
    {
        if (!$this->checkCacheDir()) {
            return '';
        }

        $filename = $this->getCacheName();
        $filename = preg_replace('/[^0-9a-z\.\_\-]/i', '', strtolower($filename));

        return $this->getCachePath() . $this->getHash($filename) . $this->getExtension();
    }

    /**
     * Get the filename hash
     *
     * @param string $filename
     *
     * @return string
     */
    private function getHash(string $filename): string
    {
        return sha1($filename);
    }

    /**
     * Check whether a timestamp is still in the duration
     *
     * @param int $timestamp
     * @param int $expiration
     *
     * @return bool
     */
    private function checkExpired(int $timestamp, int $expiration): bool
    {
        $result = false;

        if ($expiration !== 0) {
            $timeDiff = time() - $timestamp;
            if ($timeDiff > $expiration) {
                $result = true;
            }
        }

        return $result;
    }

    /**
     * Check if a writable cache directory exists and if not create a new one
     *
     * @return bool
     * @throws Exception
     */
    private function checkCacheDir(): bool
    {
        if (!is_dir($this->getCachePath()) && !mkdir($this->getCachePath(), 0775, true)) {
            throw new Exception('Unable to create cache directory ' . $this->getCachePath());
        }

        if (!is_readable($this->getCachePath()) || !is_writable($this->getCachePath())) {
            if (!chmod($this->getCachePath(), 0775)) {
                throw new Exception($this->getCachePath() . ' must be readable and writeable');
            }
        }

        return true;
    }

    /**
     * Cache path Setter
     *
     * @param string $path
     *
     * @return Leafiny_Cache
     */
    public function setCachePath(string $path): Leafiny_Cache
    {
        $this->cachePath = $path;

        return $this;
    }

    /**
     * Cache path Getter
     *
     * @return string
     */
    public function getCachePath(): string
    {
        return $this->cachePath;
    }

    /**
     * Cache name Setter
     *
     * @param string $name
     *
     * @return Leafiny_Cache
     */
    public function setCacheName(string $name): Leafiny_Cache
    {
        $this->cacheName = $name;

        return $this;
    }

    /**
     * Cache name Getter
     *
     * @return string
     */
    public function getCacheName(): string
    {
        return $this->cacheName;
    }

    /**
     * Cache file extension Setter
     *
     * @param string $extension
     *
     * @return Leafiny_Cache
     */
    public function setExtension(string $extension): Leafiny_Cache
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Cache file extension Getter
     *
     * @return string
     */
    public function getExtension(): string
    {
        return $this->extension;
    }
}
