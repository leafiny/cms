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
 * Class Commerce_Model_Shipping_Price
 */
class Commerce_Model_Shipping_Price extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_shipping_price';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = null;

    /**
     * Retrieve all prices
     *
     * @param int $shippingId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getAllPrices(int $shippingId): array
    {
        $filters = [
            [
                'column'   => 'shipping_id',
                'value'    => $shippingId,
            ]
        ];

        $orders = [
            [
                'order' => 'weight_from',
                'dir'   => 'ASC'
            ]
        ];

        return $this->getList($filters, $orders);
    }

    /**
     * Save all prices
     *
     * @param int   $shippingId
     * @param array $prices
     *
     * @return bool
     * @throws Exception
     */
    public function saveAllPrices(int $shippingId, array $prices): bool
    {
        if ($this->getMainTable() === null) {
            return false;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        foreach ($prices as $price) {
            if ($price instanceof Leafiny_Object) {
                $price = $price->toArray();
            }

            if (!isset($price['weight_from'], $price['price'])) {
                continue;
            }
            if ($price['weight_from'] === '' || $price['price'] === '') {
                continue;
            }

            foreach ($price as $key => $value) {
                $price[$key] = str_replace(',', '.', $value);
            }

            $adapter->insert(
                $this->mainTable,
                [
                    'shipping_id' => $shippingId,
                    'weight_from' => $price['weight_from'] ?: 0,
                    'price' => $price['price'] ?: 0,
                ]
            );

            if ($adapter->getLastErrno()) {
                throw new Exception($this->getAdapter()->getLastError());
            }
        }

        return true;
    }

    /**
     * Delete all prices for shipping
     *
     * @param int $shippingId
     *
     * @return bool
     * @throws Exception
     */
    public function deleteAllPrices(int $shippingId): bool
    {
        if ($this->getMainTable() === null) {
            return false;
        }

        $adapter = $this->getAdapter();
        if (!$adapter) {
            return false;
        }

        $adapter->where('shipping_id', $shippingId);
        $adapter->delete($this->getMainTable());

        if ($adapter->getLastErrno()) {
            throw new Exception($this->getAdapter()->getLastError());
        }

        return true;
    }

    /**
     * Tax validation
     *
     * @param Leafiny_Object $object
     *
     * @return string
     */
    public function validate(Leafiny_Object $object): string
    {
        if ($this->getData('skip_validation')) {
            return '';
        }

        if (!$object->getData('shipping_id')) {
            return 'The shipping cannot be empty';
        }

        return '';
    }
}
