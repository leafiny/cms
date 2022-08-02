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
 * Class Frontend_Helper_Combine
 */
abstract class Frontend_Helper_Combine extends Core_Helper
{
    /**
     * Merge resources
     *
     * @param array $resources
     * @param string $directory
     *
     * @return string
     */
    public function mergeResources(array $resources, string $directory = 'merged'): string
    {
        $dirPath = $this->getMediaDir() . trim($directory, DS) . DS;
        /** @var Core_Helper_File $fileHelper */
        $fileHelper = App::getSingleton('helper_file');

        try {
            $fileHelper->mkdir($dirPath);

            $files = $this->getFiles($resources);

            $fileName = $this->getFileName($files);
            $filePath = $dirPath . $fileName;

            if (!file_exists($filePath)) {
                foreach ($files as $file) {
                    file_put_contents($filePath, $this->getContent($file), FILE_APPEND);
                }
            }

            return $this->getMediaUrl() . $directory . US . $fileName;
        } catch (Throwable $throwable) {
            App::log($throwable, Core_Interface_Log::ERR);
        }

        return '';
    }

    /**
     * Retrieve file content
     *
     * @param Leafiny_Object $file
     *
     * @return string
     */
    public function getContent(Leafiny_Object $file): string
    {
        return file_get_contents($file->getData('path')) . "\n";
    }

    /**
     * Retrieve files
     *
     * @param array $resources
     *
     * @return Leafiny_Object[]
     */
    public function getFiles(array $resources): array
    {
        $files = [];
        foreach ($resources as $resource) {
            $path = $this->getModulesDir() . $this->getModuleFile($resource, Core_Helper::SKIN_DIRECTORY);
            $url = $this->getModuleUrl($this->getModuleFile($resource, Core_Helper::SKIN_DIRECTORY, true));
            $files[] = new Leafiny_Object(
                [
                    'path'      => $path,
                    'url'       => $url,
                    'time'      => filemtime($path),
                    'extension' => pathinfo($resource, PATHINFO_EXTENSION),
                ]
            );
        }

        return $files;
    }

    /**
     * Retrieve merged file name
     *
     * @param array $files
     *
     * @return string
     */
    public function getFileName(array $files): string
    {
        $times = [];
        $extension = 'txt';
        foreach ($files as $file) {
            $times[] = $file->getData('time');
            $extension = $file->getData('extension');
        }

        return md5(join('', $times)) . '.' . $extension;
    }
}
