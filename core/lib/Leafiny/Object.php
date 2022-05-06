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
 * Class Leafiny_Object
 */
class Leafiny_Object implements ArrayAccess
{
    /**
     * Object data
     *
     * @var array
     */
    protected $data = [];

    /**
     * Leafiny_Object Constructor
     */
    public function __construct()
    {
        $args = func_get_args();
        if (empty($args[0])) {
            $args[0] = [];
        }
        $this->data = $args[0];
    }

    /**
     * Add data
     *
     * @param array $arr
     *
     * @return Leafiny_Object
     */
    public function addData(array $arr): Leafiny_Object
    {
        foreach ($arr as $index => $value) {
            $this->setData($index, $value);
        }
        return $this;
    }

    /**
     * Set data
     *
     * @param string|array $key
     * @param mixed        $value
     *
     * @return Leafiny_Object
     */
    public function setData($key, $value = null): Leafiny_Object
    {
        if (is_array($key)) {
            $this->data = $key;
        } else {
            $this->data[$key] = $value;
        }

        return $this;
    }

    /**
     * Retrieve data
     *
     * @param string $key
     *
     * @return mixed
     */
    public function getData(string $key = '')
    {
        if ($key === '') {
            return $this->data;
        }

        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    /**
     * Unset data
     *
     * @param string|null $key
     *
     * @return Leafiny_Object
     */
    public function unsetData(?string $key = null): Leafiny_Object
    {
        if ($key) {
            unset($this->data[$key]);
        }

        return $this;
    }

    /**
     * Check data
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasData(string $key = ''): bool
    {
        if (empty($key)) {
            return !empty($this->data);
        }

        return array_key_exists($key, $this->data);
    }

    /**
     * Check data is empty
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        if (empty($this->data)) {
            return true;
        }

        return false;
    }

    /**
     * Magic method
     *
     * @param string $method
     * @param mixed  $args
     *
     * @return mixed
     * @throws Exception
     */
    public function __call(string $method, $args)
    {
        switch (substr($method, 0, 3)) {
            case 'get' :
                $key = $this->underscore(substr($method, 3));
                return $this->getData($key);
            case 'set' :
                $key = $this->underscore(substr($method, 3));
                return $this->setData($key, isset($args[0]) ? $args[0] : null);
            case 'uns' :
                $key = $this->underscore(substr($method, 3));
                return $this->unsetData($key);
        }

        throw new Exception("Invalid method " . get_class($this) . "::" . $method);
    }

    /**
     * Format method
     *
     * @param string $name
     *
     * @return string
     */
    protected function underscore(string $name): string
    {
        return strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
    }

    /**
     * Object to array
     *
     * @param array $arrAttributes
     *
     * @return array
     */
    public function toArray(array $arrAttributes = []): array
    {
        if (empty($arrAttributes)) {
            return $this->data;
        }

        $arrRes = [];
        foreach ($arrAttributes as $attribute) {
            if (isset($this->data[$attribute])) {
                $arrRes[$attribute] = $this->data[$attribute];
            } else {
                $arrRes[$attribute] = null;
            }
        }
        return $arrRes;
    }

    /**
     * offset Set
     *
     * @param mixed $offset
     * @param mixed $value
     */
    public function offsetSet($offset, $value): void
    {
        $this->data[$offset] = $value;
    }

    /**
     * Offset Exists
     *
     * @param mixed $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->data[$offset]);
    }

    /**
     * Offset Unset
     *
     * @param mixed $offset
     */
    public function offsetUnset($offset): void
    {
        unset($this->data[$offset]);
    }

    /**
     * Offset Get
     *
     * @param mixed $offset
     *
     * @return mixed|null
     */
    #[\ReturnTypeWillChange]
    public function offsetGet($offset)
    {
        return isset($this->data[$offset]) ? $this->data[$offset] : null;
    }
}
