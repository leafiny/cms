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
 * Class Commerce_Model_Sale_Address
 */
class Commerce_Model_Sale_Address extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_sale_address';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'address_id';

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
        if (!$object->getData('address_id')) {
            /** @var Commerce_Helper_Shipping $shippingHelper */
            $shippingHelper = App::getSingleton('helper', 'shipping');

            if (!$object->getData('country_code')) {
                $object->setData('country_code', $shippingHelper->getDefaultCountryCode());
            }
            if (!$object->getData('state_code')) {
                $object->setData('state_code', $shippingHelper->getDefaultStateCode());
            }
            if (!$object->getData('postcode')) {
                $object->setData('postcode', $shippingHelper->getDefaultPostcode());
            }
        }

        return parent::save($object);
    }

    /**
     * Retrieve address by sale id
     *
     * @param int    $saleId
     * @param string $type
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getBySaleId(int $saleId, string $type): ?Leafiny_Object
    {
        $filters = [
            [
                'column' => 'sale_id',
                'value'  => $saleId,
            ],
            [
                'column' => 'type',
                'value'  => $type,
            ],
        ];

        $result = parent::getList($filters, [], [0, 1]);

        if (empty($result)) {
            return null;
        }

        return reset($result);
    }
}
