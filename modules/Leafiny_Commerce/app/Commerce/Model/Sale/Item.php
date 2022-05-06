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
 * Class Commerce_Model_Sale_Item
 */
class Commerce_Model_Sale_Item extends Core_Model
{
    /**
     * Main Table
     *
     * @var string $mainTable
     */
    protected $mainTable = 'commerce_sale_item';
    /**
     * Primary key
     *
     * @var string $primaryKey
     */
    protected $primaryKey = 'item_id';

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
        $qty = (int)$object->getData('qty');
        $inclTaxUnit = $object->getData('incl_tax_unit');
        $exclTaxUnit = $object->getData('excl_tax_unit');
        $weightUnit  = $object->getData('weight_unit');

        $weightRow     = $weightUnit * $qty;
        $inclTaxRow    = $inclTaxUnit * $qty;
        $exclTaxRow    = $exclTaxUnit * $qty;
        $taxAmountUnit = $inclTaxUnit - $exclTaxUnit;
        $taxAmountRow  = $inclTaxRow - $exclTaxRow;

        $object->addData(
            [
                'incl_tax_row'    => $inclTaxRow,
                'excl_tax_row'    => $exclTaxRow,
                'tax_amount_unit' => $taxAmountUnit,
                'tax_amount_row'  => $taxAmountRow,
                'weight_row'      => $weightRow,
            ]
        );

        return parent::save($object);
    }

    /**
     * Retrieve item in sale
     *
     * @param int $saleId
     * @param mixed $value
     * @param string|null $column
     *
     * @return Leafiny_Object
     * @throws Exception
     */
    public function getItem(int $saleId, $value, ?string $column = null): Leafiny_Object
    {
        $adapter = $this->getAdapter();
        if (!$adapter) {
            return new Leafiny_Object();
        }

        $adapter->where('main_table.sale_id', $saleId);

        return parent::get($value, $column);
    }

    /**
     * Retrieve items for sale id
     *
     * @param int $saleId
     *
     * @return Leafiny_Object[]
     * @throws Exception
     */
    public function getItems(int $saleId): array
    {
        $filters = [
            [
                'column' => 'sale_id',
                'value'  => $saleId,
            ]
        ];

        return parent::getList($filters);
    }
}
