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
 * Class Commerce_Model_Sale
 */
class Commerce_Model_Sale extends Core_Model
{
    public const SALE_STATE_CART  = 'cart';
    public const SALE_STATE_ORDER = 'order';

    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_sale';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'sale_id';

    /**
     * Save
     *
     * @param Leafiny_Object $object
     *
     * @return int|null
     * @throws Exception
     */
    public function save(Leafiny_Object $object): ?int
    {
        $isNew = !$object->getData('sale_id');

        if ($isNew && !$object->getData('language')) {
            $object->setData('language', App::getLanguage());
        }

        if ($isNew && !$object->getData('key')) {
            $object->setData('key', $this->generateKey());
        }

        return parent::save($object);
    }

    /**
     * Set order invoice increment id
     *
     * @param int $saleId
     *
     * @return string
     * @throws Exception
     */
    public function invoice(int $saleId): string
    {
        $sale = $this->get($saleId);

        if (!$sale->getData($this->getPrimaryKey())) {
            throw new Exception('The order no longer exists');
        }
        if ($sale->getData('state') !== self::SALE_STATE_ORDER) {
            throw new Exception('The order is not ready for invoicing');
        }
        if ($sale->getData('invoice_increment_id')) {
            throw new Exception('The order is already invoiced');
        }

        $incrementId = $this->getNextIncrementId('invoice_increment_id');

        $sale->setData('invoice_increment_id', $incrementId);

        $this->save($sale);

        return $incrementId;
    }

    /**
     * Retrieve order by key
     *
     * @param string $key
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getByKey(string $key): Leafiny_Object
    {
        return $this->get($key, 'key');
    }

    /**
     * Generate a random key
     *
     * @return string
     */
    public function generateKey(): string
    {
        $key = uniqid('', true) . '.' . rand(10000000, 99999999);

        return str_shuffle(substr(base64_encode(str_repeat($key, 5)), 0, 50));
    }

    /**
     * Retrieve next increment id
     *
     * @param string $column
     *
     * @return string
     * @throws Exception
     */
    public function getNextIncrementId(string $column): string
    {
        /** @var Commerce_Helper_Order $orderHelper */
        $orderHelper = App::getSingleton('helper', 'order');
        $default = $orderHelper->getDefaultIncrementId($column);

        if ($this->getMainTable() === null) {
            return $default;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return $default;
        }

        $adapter->where('state', self::SALE_STATE_ORDER);
        $adapter->where($column, null, 'IS NOT');
        $adapter->orderBy($this->getPrimaryKey());
        $result = $adapter->getOne($this->getMainTable(), [$column]);

        if (!$result) {
            return $default;
        }

        $incrementId = $result[$column];

        if (!$incrementId) {
            return $default;
        }

        $prefix = (string)preg_replace('/[0-9]/', '', $incrementId);
        $number = (string)preg_replace('/[^0-9]/', '', $incrementId);

        $increment = (int)$number + 1;

        return $prefix . str_pad((string)$increment, strlen($number), '0', STR_PAD_LEFT);
    }
}
