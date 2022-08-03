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
 * Class Leafiny_Config
 */
class Leafiny_Config
{
    /**
     * Type keys constant
     *
     * @var string TYPE_KEYS
     */
    public const TYPE_KEYS = '_keys';

    /**
     * PHP Config files
     *
     * @var array $files
     */
    protected $files = [];

    /**
     * Merged configurations
     *
     * @var array $result
     */
    protected $result = [];

    /**
     * Set config files
     *
     * @param array $files
     *
     * @return void
     */
    public function setFiles($files): void
    {
        $this->files = $files;
    }

    /**
     * Retrieve config value
     *
     * @param string $path
     *
     * @return array|string|bool
     */
    public function get($path)
    {
        return $this->result[$path] ?? false;
    }

    /**
     * Set config value
     *
     * @param string $path
     * @param mixed  $value
     *
     * @return void
     */
    public function set($path, $value): void
    {
        $this->result[$path] = $value;
    }

    /**
     * Load config
     *
     * @param bool $flat
     *
     * @return void
     */
    public function load(bool $flat = true): void
    {
        $result = [];

        foreach ($this->files as $file) {
            $result = array_replace_recursive($result, $this->loadFile($file));
        }

        $this->result = $flat ? $this->flat($result) : $result;
    }

    /**
     * Flat config
     *
     * @param array  $config
     * @param array  $result
     * @param string $flat
     *
     * @return array
     */
    protected function flat(array $config, array $result = [], string $flat = ''): array
    {
        if (!empty($flat)) {
            if (!isset($result[$flat])) {
                $result[$flat] = [];
            }
            $result[$flat][self::TYPE_KEYS] = array_keys($config);
        }

        foreach ($config as $key => $value) {
            if (is_array($value)) {
                $result = $this->flat($value, $result, ltrim($flat . '.' . $key, '.'));
            } else {
                $result[$flat] = $config;
                $result[$flat . '.' . $key] = $value;
            }
        }

        return $result;
    }

    /**
     * Load file config
     *
     * @param string $file
     *
     * @return array[]
     */
    protected function loadFile(string $file): array
    {
        if (is_file($file)) {
            include $file;

            if (!isset($config)) {
                return [];
            }

            return $config;
        }

        return [];
    }
}
