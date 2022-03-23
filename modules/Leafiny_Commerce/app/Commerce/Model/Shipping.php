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
 * Class Commerce_Model_Shipping
 */
class Commerce_Model_Shipping extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_shipping';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'shipping_id';

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
        $method = preg_replace('/[^a-z]/', '_', strtolower($object->getData('method')));
        $object->setData('method', $method);

        $countries = $object->getData('countries') ?: '*';
        if ($countries instanceof Leafiny_Object) {
            $countries = $countries->toArray();
        }
        if (is_array($countries)) {
            $countries = join(',', $countries);
        }
        $object->setData('countries', $countries);

        if (!$object->getData('states')) {
            $object->setData('states', '*');
        }
        if (!$object->getData('postcodes')) {
            $object->setData('postcodes', '*');
        }
        if (!$object->getData('price_lines')) {
            $object->setData('price_lines', 1);
        }

        $prices = $object->getData('prices') ?: [];
        if ($prices instanceof Leafiny_Object) {
            $prices = $prices->toArray();
        }
        $prices = array_splice($prices, 0, (int)$object->getData('price_lines'));

        $shippingId = parent::save($object);

        if ($shippingId) {
            /** @var Commerce_Model_Shipping_Price $model */
            $model = App::getObject('model', 'shipping_price');
            $model->deleteAllPrices($shippingId);
            $model->saveAllPrices($shippingId, $prices);
        }

        return $shippingId;
    }

    /**
     * Retrieve data
     *
     * @param mixed $value
     * @param string|null $column
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function get($value, ?string $column = null): Leafiny_Object
    {
        $result = parent::get($value, $column);

        /** @var Commerce_Model_Shipping_Price $model */
        $model = App::getObject('model', 'shipping_price');

        $result->setData('prices', $model->getAllPrices((int)$result->getData('shipping_id')));

        return $result;
    }

    /**
     * Shipping validation
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

        if (!$object->getData('label')) {
            return 'The label cannot be empty';
        }
        if (!$object->getData('method')) {
            return 'The method cannot be empty';
        }

        return '';
    }
}
