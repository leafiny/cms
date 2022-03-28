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
 * Class Commerce_Model_Sale_Shipment
 */
class Commerce_Model_Sale_Shipment extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_sale_shipment';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'shipment_id';

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
        if ($object->getData('tracking_number') && $object->getData('tracking_url')) {
            $object->setData(
                'tracking_url',
                str_replace('{tracking_number}', $object->getData('tracking_number'), $object->getData('tracking_url'))
            );
        }

        return parent::save($object);
    }

    /**
     * Retrieve shipments by sale id
     *
     * @param int $saleId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getBySaleId(int $saleId): array
    {
        $filters = [
            [
                'column' => 'sale_id',
                'value'  => $saleId,
            ],
        ];

        $orders = [
            [
                'order' => 'created_at',
                'dir'   => 'ASC',
            ]
        ];

        return parent::getList($filters, $orders);
    }
}
