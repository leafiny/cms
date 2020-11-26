<?php

declare(strict_types=1);

/**
 * Class Leafiny_Translate
 */
class Leafiny_Translate
{
    /**
     * Csv files
     *
     * @var array $files
     */
    protected $files = [];
    /**
     * Merged files
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
     * Add translation file
     *
     * @param string $file
     */
    public function addFile(string $file): void
    {
        $this->files[] = $file;
    }

    /**
     * Load translations
     *
     * @return void
     */
    public function load(): void
    {
        foreach ($this->files as $file) {
            if (!is_file($file)) {
                continue;
            }

            $fh = fopen($file, 'r');
            while ($rowData = fgetcsv($fh, 0, ',', '"')) {
                if (!isset($rowData[0])) {
                    continue;
                }

                $this->result[$rowData[0]] = isset($rowData[1]) ? (string)$rowData[1] : null;
            }
            fclose($fh);
        }
    }

    /**
     * Retrieve translation
     *
     * @param string $key
     *
     * @return string
     */
    public function get(string $key): string
    {
        if (!isset($this->result[$key])) {
            return $key;
        }

        if (!$this->result[$key]) {
            return $key;
        }

        return $this->result[$key];
    }
}
