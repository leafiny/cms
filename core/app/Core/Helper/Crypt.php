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
 * Class Core_Helper_Crypt
 */
class Core_Helper_Crypt extends Core_Helper
{
    /**
     * @var string KEY_FILE
     */
    public const KEY_FILE = 'crypt.key';
    /**
     * @var string CIPHER_ALGO
     */
    public const CIPHER_ALGO = 'aes-128-ctr';
    /**
     * @var string VECTOR_SIZE
     */
    public const VECTOR_SIZE = 16;

    /**
     * @var string $key
     */
    private $key = '';
    /**
     * @var string $iv
     */
    private $iv = '';

    /**
     * Core_Helper_Crypt constructor
     */
    public function __construct()
    {
        parent::__construct();

        if (!is_file($this->getKeyFile())) {
            $this->saveKey();
        }

        $content = file_get_contents($this->getKeyFile());

        if ($content) {
            $key = explode('::', $content);

            $this->iv  = isset($key[0]) ? $key[0] : '';
            $this->key = isset($key[1]) ? $key[1] : '';
        }
    }

    /**
     * Crypt message
     *
     * @param string $message
     *
     * @return string
     */
    public function crypt(string $message): string
    {
        $encrypt = openssl_encrypt($message, self::CIPHER_ALGO, $this->getKey(), 0, $this->getIv());

        if (!$encrypt) {
            return '';
        }

        return strtr($encrypt, '+/=', '-_,');
    }

    /**
     * Decrypt message
     *
     * @param string $message
     *
     * @return string
     */
    public function decrypt(string $message): string
    {
        $decrypt = strtr($message, '-_,', '+/=');

        return openssl_decrypt($decrypt, self::CIPHER_ALGO, $this->getKey(), 0, $this->getIv()) ?: '';
    }

    /**
     * Generate a random key with iv
     *
     * @return string
     */
    protected function generate(): string
    {
        return $this->generateIv() . '::' . $this->generateKey();
    }

    /**
     * Generate a random iv
     *
     * @return string
     */
    protected function generateIv(): string
    {
        return substr(hash('sha256', uniqid()), 0, self::VECTOR_SIZE);
    }

    /**
     * Generate a random key
     *
     * @return string
     */
    protected function generateKey(): string
    {
        return uniqid('', true) . '.' . rand(10000000, 99999999);
    }

    /**
     * Retrieve key file
     *
     * @return string
     */
    protected function getKeyFile(): string
    {
        return $this->getCryptDir() . self::KEY_FILE;
    }

    /**
     * Save a new key
     *
     * @return bool
     */
    protected function saveKey(): bool
    {
        return (bool)file_put_contents($this->getKeyFile(), $this->generate());
    }

    /**
     * Retrieve key
     *
     * @return string
     */
    protected function getKey(): string
    {
        return (string)$this->key;
    }

    /**
     * Retrieve IV
     *
     * @return string
     */
    protected function getIv(): string
    {
        return (string)$this->iv;
    }
}
