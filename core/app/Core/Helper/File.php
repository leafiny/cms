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
 * Class Core_Helper_File
 */
class Core_Helper_File extends Core_Helper
{
    /**
     * Retrieve max allowed file size
     *
     * @return int
     */
    public function getMaxFileSize(): int
    {
        return min((int)ini_get('post_max_size'), (int)ini_get('upload_max_filesize'));
    }

    /**
     * Resize an image and keep the proportions
     * New image is stored in sub directory
     * Return the new filename with sub directory
     *
     * @param string      $directory
     * @param string      $filename
     * @param int         $maxWidth
     * @param int         $maxHeight
     * @param int         $quality
     * @param string|null $newName
     * @param string|null $toExt
     * @param string      $sub
     *
     * @return string|null
     */
    function imageResize(
        string $directory,
        string $filename,
        int $maxWidth,
        int $maxHeight,
        int $quality = 100,
        string $newName = null,
        string $toExt = null,
        string $sub = 'cache'
    ): ?string
    {
        $info = pathinfo($filename);
        $directory = rtrim($directory, DS);
        $filename = $directory . DS . $filename;

        if (!is_file($filename)) {
            return null;
        }

        if (!isset($info['extension'], $info['dirname'], $info['filename'])) {
            return null;
        }

        $size = $maxWidth === $maxHeight ? $maxWidth : $maxWidth . 'x' . $maxHeight;

        $toExt = $toExt ?: $info['extension'];

        if ($newName !== null) {
            $info['filename'] = $this->formatKey($newName);
        }

        $file = $info['filename'] . '-' . $size . '.' . $toExt;

        $resizeDirectory = $directory . DS . $info['dirname'] . DS . ($sub ? $sub . DS : '');

        try {
            $this->mkdir($resizeDirectory);
        } catch (Throwable $throwable) {
            return null;
        }

        if (!is_writable($resizeDirectory)) {
            return null;
        }

        $destination = $resizeDirectory . $file;

        if (is_file($destination)) {
            return $info['dirname'] . ($sub ? '/' . $sub . '/' : '/') . $file;
        }

        list($origWidth, $origHeight) = getimagesize($filename);

        $width  = $origWidth;
        $height = $origHeight;

        if ($height > $maxHeight) {
            $width = ($maxHeight / $height) * $width;
            $height = $maxHeight;
        }

        if ($width > $maxWidth) {
            $height = ($maxWidth / $width) * $height;
            $width = $maxWidth;
        }

        $result = imagecreatetruecolor((int)$width, (int)$height);

        if (!$result) {
            return null;
        }

        $image = null;

        if ($info['extension'] === 'jpg') {
            $image = imagecreatefromjpeg($filename);
        }
        if ($info['extension'] === 'jpeg') {
            $image = imagecreatefromjpeg($filename);
        }
        if ($info['extension'] === 'png') {
            $image = imagecreatefrompng($filename);
        }
        if ($info['extension'] === 'gif') {
            $image = imagecreatefromgif($filename);
        }

        if (!$image) {
            return null;
        }

        $copy = imagecopyresampled(
            $result,
            $image,
            0,
            0,
            0,
            0,
            (int)$width,
            (int)$height,
            (int)$origWidth,
            (int)$origHeight
        );

        if (!$copy) {
            return null;
        }

        $resized = null;

        if ($toExt === 'jpg') {
            $resized = imagejpeg($result, $destination, $quality);
        }
        if ($toExt === 'jpeg') {
            $resized = imagejpeg($result, $destination, $quality);
        }
        if ($toExt === 'webp') {
            $resized = imagewebp($result, $destination, $quality);
        }
        if ($toExt === 'png') {
            $resized = imagepng($result, $destination);
        }
        if ($toExt === 'gif') {
            $resized = imagegif($result, $destination);
        }

        if (!$resized) {
            return null;
        }

        return $info['dirname'] . ($sub ? '/' . $sub . '/' : '/') . $file;
    }

    /**
     * Upload file and throw error if needed
     *
     * @param array      $uploadedFile
     * @param string     $destination
     * @param array|null $allowedExtensions
     *
     * @return bool
     * @throws Exception
     */
    public function uploadFile(array $uploadedFile, string $destination, ?array $allowedExtensions = null): bool
    {
        $errorPrefix = 'Error when uploaded file: ';

        if (!isset($uploadedFile['name'], $uploadedFile['tmp_name'], $uploadedFile['size'], $uploadedFile['error'])) {
            throw new Exception($errorPrefix . 'wrong file data');
        }

        if ($uploadedFile['error']) {
            throw new Exception($errorPrefix . $this->getFileUploadErrorMessage($uploadedFile['error']));
        }

        $directory = dirname($destination);

        $this->mkdir($directory);

        $fileInfo = pathinfo($uploadedFile['name']);

        if ($allowedExtensions) {
            if (!in_array($fileInfo['extension'], $allowedExtensions)) {
                throw new Exception($errorPrefix . $fileInfo['extension'] . ' is not allowed');
            }
        }

        if (!is_writable($directory)) {
            throw new Exception($errorPrefix . 'directory is not writable');
        }

        if (!move_uploaded_file($uploadedFile['tmp_name'], $destination)) {
            throw new Exception($errorPrefix . 'something went wrong');
        }

        return true;
    }

    /**
     * Create directory
     *
     * @param string $directory
     *
     * @return bool
     * @throws Exception
     */
    protected function mkdir(string $directory): bool
    {
        if (!is_dir($directory)) {
            @mkdir($directory, 0777, true);
            $error = error_get_last();
            if (isset($error['message'])) {
                throw new Exception($error['message']);
            }
        }

        return true;
    }

    /**
     * Unlink file
     *
     * @param string $file
     *
     * @return bool
     */
    public function unlink(string $file): bool
    {
        if (is_file($file)) {
            @unlink($file);
            return true;
        }

        return false;
    }

    /**
     * Remove directory recursive
     *
     * @param string $directory
     * @param int    $count
     *
     * @return int
     * @throws Exception
     */
    public function rmdir(string $directory, int &$count = 0): int
    {
        $directory = rtrim($directory, DS) . DS;

        $files = @glob($directory . '*');

        foreach ($files as $file) {
            if ($this->unlink($file)) {
                $count++;
            }
            if (is_dir($file)) {
                $this->rmdir($file, $count);
            }
        }

        if (count(glob($directory . '*')) === 0) {
            @rmdir($directory);
        }

        $error = error_get_last();
        if (isset($error['message'])) {
            throw new Exception($error['message']);
        }

        return $count;
    }

    /**
     * Retrieve file upload error
     *
     * @param int $error
     *
     * @return string
     */
    protected function getFileUploadErrorMessage(int $error): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'the uploaded file exceeds the UPLOAD_MAX_FILESIZE directive',
            UPLOAD_ERR_FORM_SIZE  => 'the uploaded file exceeds the MAX_FILE_SIZE directive',
            UPLOAD_ERR_PARTIAL    => 'the uploaded file was only partially uploaded',
            UPLOAD_ERR_NO_FILE    => 'no file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'missing a temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'failed to write file to disk',
            UPLOAD_ERR_EXTENSION  => 'an extension stopped the file upload'
        ];

        if (isset($errors[$error])) {
            return $errors[$error];
        }

        return 'something went wrong';
    }
}
